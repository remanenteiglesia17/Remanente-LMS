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

class ProfesorSeeder extends Seeder
{
    public function run(): void
    {
        //--------------------------------------------]
        $user = User::create([
            'name' => 'Profesor',
            'lastname' => 'Lewis',
            'email' => 'profesor@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('profesor');
        // nombres/apellidos ya no se guardan aquí (viven en 'users', arriba)
        Profesor::create([
            'telefono' => '4564564565',
            'user_id' => $user->id,
        ]);

        $user = User::create([
            'name' => 'TEACHER',
            'lastname' => 'Gallardo',
            'email' => 'profesor1@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('profesor');
        Profesor::create([
            'telefono' => '432324324',
            'user_id' => $user->id,
        ]);

        $user = User::create([
            'name' => 'Julio Profe',
            'lastname' => 'Valdes',
            'email' => 'profesor2@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123213'),
        ]);
        $user->assignRole('profesor');
        Profesor::create([
            'telefono' => '123123213',
            'user_id' => $user->id,
        ]);

        $user = User::create([
            'name' => 'Martin Profe',
            'lastname' => 'Valdes',
            'email' => 'profesor3@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123213'),
        ]);
        $user->assignRole('profesor');
        Profesor::create([
            'telefono' => '123123213',
            'user_id' => $user->id,
        ]);
        //--------------------------------------------]
    }
}
