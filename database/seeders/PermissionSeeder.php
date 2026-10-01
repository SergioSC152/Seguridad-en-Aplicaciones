<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'content.manage', 'label' => 'Administrar contenido', 'description' => 'Crear y mantener noticias y archivos multimedia.'],
            ['code' => 'mail-settings.manage', 'label' => 'Administrar correo SMTP', 'description' => 'Cambiar la configuración del correo saliente.'],
            ['code' => 'clients.manage', 'label' => 'Administrar clientes', 'description' => 'Registrar y mantener clientes ganaderos de la cuenta.'],
            ['code' => 'sales-pipeline.manage', 'label' => 'Administrar Pipeline comercial', 'description' => 'Gestionar oportunidades comerciales de clientes propios.'],
            ['code' => 'leads.manage', 'label' => 'Administrar leads y prospectos', 'description' => 'Registrar y dar seguimiento a prospectos de la cuenta.'],
        ] as $permission) {
            Permission::query()->updateOrCreate(['code' => $permission['code']], $permission);
        }
    }
}
