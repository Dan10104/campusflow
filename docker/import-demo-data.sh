#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

SQL_FILE="${1:-database/scripts/data.sql}"

if [ ! -f "${SQL_FILE}" ]; then
    echo "ERROR: No se encontro ${SQL_FILE}."
    exit 1
fi

if [ "${DB_CONNECTION:-mysql}" != "mysql" ]; then
    echo "ERROR: Este script manual esta preparado solo para DB_CONNECTION=mysql."
    exit 1
fi

DB_HOST="${DB_HOST:-mysql}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-ProyectoFinal}"
DB_USERNAME="${DB_USERNAME:-campusflow}"
DB_PASSWORD="${DB_PASSWORD:-}"
DB_SOCKET="${DB_SOCKET:-}"

MYSQL_CONNECTION_ARGS=()
MYSQL_CONNECTION_ARGS+=(--default-character-set=utf8mb4)

if [ -n "${DB_SOCKET}" ]; then
    MYSQL_CONNECTION_ARGS+=(--socket="${DB_SOCKET}")
else
    MYSQL_CONNECTION_ARGS+=(-h "${DB_HOST}" -P "${DB_PORT}")
fi

TABLES=(
    facultades
    modulos
    roles
    permisos
    users
    aulas
    tipo_activo
    activos
    reservas
    reservas_activos
    checkins_reserva
    sensores
    eventos_sensor
    notificaciones
    bitacora
    asientos_blockchain
)

echo "Verificando migraciones y datos existentes antes de importar..."
php artisan migrate:status >/dev/null

non_empty_tables=()

for table in "${TABLES[@]}"; do
    count="$(
        MYSQL_PWD="${DB_PASSWORD}" mysql \
            "${MYSQL_CONNECTION_ARGS[@]}" \
            -u "${DB_USERNAME}" \
            --batch \
            --skip-column-names \
            "${DB_DATABASE}" \
            -e "SELECT COUNT(*) FROM \`${table}\`;" 2>/tmp/campusflow-import-check.log
    )" || {
        echo "ERROR: No se pudo consultar la tabla ${table}. Ejecuta migraciones antes de importar."
        cat /tmp/campusflow-import-check.log || true
        exit 1
    }

    if [ "${count}" != "0" ]; then
        non_empty_tables+=("${table}:${count}")
    fi
done

if [ "${#non_empty_tables[@]}" -gt 0 ]; then
    echo "ABORTADO: se detectaron datos existentes en tablas de demostracion:"
    printf ' - %s\n' "${non_empty_tables[@]}"
    echo "No se importo ${SQL_FILE} para evitar duplicados o inconsistencias."
    exit 1
fi

echo "Todas las tablas de demostracion revisadas estan vacias."
echo "Archivo a importar: ${SQL_FILE}"
printf 'Escribe IMPORTAR para continuar: '
read -r confirmation

if [ "${confirmation}" != "IMPORTAR" ]; then
    echo "Importacion cancelada por el usuario."
    exit 0
fi

MYSQL_PWD="${DB_PASSWORD}" mysql \
    "${MYSQL_CONNECTION_ARGS[@]}" \
    -u "${DB_USERNAME}" \
    "${DB_DATABASE}" < "${SQL_FILE}"

echo "Datos de demostracion importados correctamente."
