<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $path = database_path('scripts/data.sql');

        if (!File::exists($path)) {
            $this->command->error('El archivo SQL no existe en: ' . $path);
            return;
        }

        $this->command->info('Iniciando importación masiva...');

        // 1. DESACTIVAR VERIFICACIÓN DE LLAVES FORÁNEAS
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Ejecutar el SQL completo
        try {
            $sql = File::get($path);
            DB::unprepared($sql);
            $this->command->info('Datos importados correctamente.');
        } catch (\Exception $e) {
            $this->command->error('Error al importar: ' . $e->getMessage());
        }

        // 3. REACTIVAR VERIFICACIÓN DE LLAVES FORÁNEAS
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
