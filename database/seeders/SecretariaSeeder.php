<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Profesor;
use App\Models\Horario;
use App\Models\Estudiante;
use App\Models\Secretaria;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SecretariaSeeder extends Seeder
{
    public function run(): void
    {
         //----------[  SECRETARIA  ]-------------
         $user = User::create([
            'name' => 'Secretaria',
            'lastname' => 'Catrana',
            'email' => 'secretaria@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('secretaria');

        // nombres/apellidos ya no se guardan aquí (viven en 'users', arriba)
        Secretaria::create([
            'cc' => '1112036545',
            'telefono' => '3147078256',
            'fecha_nacimiento' => '22/10/2010',
            'direccion' => 'calle 5 o este',
            'user_id' => $user->id,
        ]);
        // -------------------------------------------------
    }
}
