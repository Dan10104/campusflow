<?php

namespace App\Exports;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class DashboardExport
{
    public function __construct(
        private array $datos,
        private CarbonInterface $fechaGeneracion
    ) {}

    public function download(string $filename): BinaryFileResponse
    {
        $path = $this->crearArchivoXlsx();

        return response()
            ->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    private function crearArchivoXlsx(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'campusflow-dashboard-');

        if ($path === false) {
            throw new RuntimeException('No se pudo preparar el archivo temporal del reporte.');
        }

        $xlsxPath = $path . '.xlsx';
        rename($path, $xlsxPath);

        $zip = new ZipArchive();

        if ($zip->open($xlsxPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('No se pudo crear el archivo Excel del Dashboard.');
        }

        $sheets = $this->sheets();

        $zip->addFromString('[Content_Types].xml', $this->contentTypes(count($sheets)));
        $zip->addFromString('_rels/.rels', $this->rootRels());
        $zip->addFromString('docProps/app.xml', $this->appProperties($sheets));
        $zip->addFromString('docProps/core.xml', $this->coreProperties());
        $zip->addFromString('xl/workbook.xml', $this->workbook($sheets));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRels(count($sheets)));
        $zip->addFromString('xl/styles.xml', $this->styles());

        foreach ($sheets as $index => $sheet) {
            $zip->addFromString(
                'xl/worksheets/sheet' . ($index + 1) . '.xml',
                $this->worksheet($sheet['rows'])
            );
        }

        $zip->close();

        return $xlsxPath;
    }

    private function sheets(): array
    {
        return [
            [
                'name' => 'Resumen general',
                'rows' => $this->resumenGeneral(),
            ],
            [
                'name' => 'Reservas por estado',
                'rows' => $this->reservasPorEstado(),
            ],
            [
                'name' => 'Aulas solicitadas',
                'rows' => $this->aulasSolicitadas(),
            ],
            [
                'name' => 'Próximas reservas',
                'rows' => $this->proximasReservas(),
            ],
            [
                'name' => 'Activos mantenimiento',
                'rows' => $this->activosMantenimiento(),
            ],
            [
                'name' => 'Predicciones',
                'rows' => $this->predicciones(),
            ],
        ];
    }

    private function resumenGeneral(): array
    {
        return [
            ['Indicador', 'Valor'],
            ['Reservas este mes', data_get($this->datos, 'kpis.reservas_mes', 0)],
            ['Activos prestados', data_get($this->datos, 'kpis.activos_prestados', 0)],
            ['Aulas en mantenimiento', data_get($this->datos, 'kpis.aulas_mantenimiento', 0)],
            ['Fecha de generación', $this->fechaGeneracion->format('d/m/Y H:i')],
        ];
    }

    private function reservasPorEstado(): array
    {
        $rows = [['Estado', 'Total']];
        $items = $this->items(data_get($this->datos, 'charts.estados', []));

        foreach ($items as $item) {
            $rows[] = [
                $this->estado(data_get($item, 'estado')),
                data_get($item, 'total', 0),
            ];
        }

        return $this->conFilaVacia($rows, 2);
    }

    private function aulasSolicitadas(): array
    {
        $rows = [['Aula', 'Total de reservas']];
        $items = $this->items(data_get($this->datos, 'charts.aulas_top', []));

        foreach ($items as $item) {
            $rows[] = [
                data_get($item, 'nombre', 'No disponible'),
                data_get($item, 'total', 0),
            ];
        }

        return $this->conFilaVacia($rows, 2);
    }

    private function proximasReservas(): array
    {
        $rows = [['Aula', 'Código de aula', 'Estado', 'Inicio']];
        $items = $this->items(data_get($this->datos, 'listas.proximas_reservas', []));

        foreach ($items as $item) {
            $rows[] = [
                trim((string) data_get($item, 'aula.modulo.nombre', 'No disponible')),
                data_get($item, 'aula_codigo', 'No disponible'),
                $this->estado(data_get($item, 'estado')),
                $this->fecha(data_get($item, 'inicio')),
            ];
        }

        return $this->conFilaVacia($rows, 4);
    }

    private function activosMantenimiento(): array
    {
        $rows = [['Código', 'Descripción', 'Tipo']];
        $items = $this->items(data_get($this->datos, 'listas.activos_mantenimiento', []));

        foreach ($items as $item) {
            $rows[] = [
                data_get($item, 'codigo', 'No disponible'),
                data_get($item, 'descripcion', 'No disponible'),
                data_get($item, 'tipo_activo.nombre')
                    ?? data_get($item, 'tipoActivo.nombre')
                    ?? 'No disponible',
            ];
        }

        return $this->conFilaVacia($rows, 3);
    }

    private function predicciones(): array
    {
        $rows = [['Aula', 'Nivel de demanda', 'Probabilidad', 'Hora pico']];
        $items = $this->items(data_get($this->datos, 'listas.predicciones', []));

        foreach ($items as $item) {
            $rows[] = [
                data_get($item, 'aula', 'No disponible'),
                data_get($item, 'nivel_demanda', 'No disponible'),
                data_get($item, 'probabilidad', 0),
                data_get($item, 'hora_pico', 'No disponible'),
            ];
        }

        return $this->conFilaVacia($rows, 4);
    }

    private function conFilaVacia(array $rows, int $columns): array
    {
        if (count($rows) > 1) {
            return $rows;
        }

        return [
            ...$rows,
            array_pad(['No existen registros disponibles para este indicador.'], $columns, ''),
        ];
    }

    private function items(mixed $value): array
    {
        if ($value instanceof Collection) {
            return $value->all();
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_iterable($value)) {
            return iterator_to_array($value);
        }

        return [];
    }

    private function estado(mixed $value): string
    {
        $estado = trim(str_replace('_', ' ', (string) $value));

        return $estado !== '' ? ucfirst($estado) : 'No disponible';
    }

    private function fecha(mixed $value): string
    {
        if (! $value) {
            return 'No disponible';
        }

        try {
            return Carbon::parse($value)->format('d/m/Y H:i');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function worksheet(array $rows): string
    {
        $xmlRows = '';

        foreach ($rows as $rowIndex => $row) {
            $number = $rowIndex + 1;
            $xmlRows .= '<row r="' . $number . '">';

            foreach (array_values($row) as $columnIndex => $value) {
                $cell = $this->columnName($columnIndex + 1) . $number;
                $style = $rowIndex === 0 ? ' s="1"' : '';

                if (is_int($value) || is_float($value)) {
                    $xmlRows .= '<c r="' . $cell . '"' . $style . '><v>' . $value . '</v></c>';
                    continue;
                }

                $xmlRows .= '<c r="' . $cell . '" t="inlineStr"' . $style . '><is><t>'
                    . $this->xml((string) $value)
                    . '</t></is></c>';
            }

            $xmlRows .= '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18"/>'
            . '<cols><col min="1" max="8" width="28" customWidth="1"/></cols>'
            . '<sheetData>' . $xmlRows . '</sheetData>'
            . '</worksheet>';
    }

    private function contentTypes(int $sheetCount): string
    {
        $worksheets = '';

        for ($index = 1; $index <= $sheetCount; $index++) {
            $worksheets .= '<Override PartName="/xl/worksheets/sheet' . $index . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $worksheets
            . '</Types>';
    }

    private function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function workbook(array $sheets): string
    {
        $sheetXml = '';

        foreach ($sheets as $index => $sheet) {
            $id = $index + 1;
            $sheetXml .= '<sheet name="' . $this->xml($sheet['name']) . '" sheetId="' . $id . '" r:id="rId' . $id . '"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheetXml . '</sheets>'
            . '</workbook>';
    }

    private function workbookRels(int $sheetCount): string
    {
        $relationships = '';

        for ($index = 1; $index <= $sheetCount; $index++) {
            $relationships .= '<Relationship Id="rId' . $index . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $index . '.xml"/>';
        }

        $relationships .= '<Relationship Id="rId' . ($sheetCount + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $relationships
            . '</Relationships>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/><color rgb="FFFFFFFF"/></font></fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF2563EB"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="1" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private function appProperties(array $sheets): string
    {
        $titles = '';

        foreach ($sheets as $sheet) {
            $titles .= '<vt:lpstr>' . $this->xml($sheet['name']) . '</vt:lpstr>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>CampusFlow</Application>'
            . '<DocSecurity>0</DocSecurity>'
            . '<ScaleCrop>false</ScaleCrop>'
            . '<HeadingPairs><vt:vector size="2" baseType="variant"><vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant><vt:variant><vt:i4>' . count($sheets) . '</vt:i4></vt:variant></vt:vector></HeadingPairs>'
            . '<TitlesOfParts><vt:vector size="' . count($sheets) . '" baseType="lpstr">' . $titles . '</vt:vector></TitlesOfParts>'
            . '<Company>Nexora Tech</Company>'
            . '</Properties>';
    }

    private function coreProperties(): string
    {
        $timestamp = $this->fechaGeneracion->toAtomString();

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:title>Reporte del Dashboard de CampusFlow</dc:title>'
            . '<dc:creator>CampusFlow</dc:creator>'
            . '<cp:lastModifiedBy>CampusFlow</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $timestamp . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $timestamp . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    private function columnName(int $number): string
    {
        $name = '';

        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
