<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Seeder;

class EstudianteSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------]
        // -------------[ Estudiante ]----------------------
        $user = User::create([
            'name' => 'Estudiante',
            'lastname' => 'alracona',
            'email' => 'estudiante@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');

        // nombres/apellidos ya no se guardan aquí (viven en 'users', arriba)
        $estudiante = Estudiante::create([
            'cc' => '12312753',
            'genero' => 'M',
            'telefono' => '12395113',
            'direccion' => 'Cll 9 oeste',
            'contacto_emergencia' => '65495113',
            'observaciones' => 'le irrita estar cerca del povo',
            'user_id' => $user->id,
        ]);
        // Relacionar con los cursos (asumiendo que los cursos ya existen)
        $cursos = Curso::whereIn('id', [1])->get(); // Obtener curso con ID 1
        $estudiante->cursos()->attach($cursos); // Crear las relaciones

        $user = User::create([
            'name' => 'Fancisco Antonio',
            'lastname' => 'Grijalba',
            'email' => 'francisco.grijalba@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');
        $estudiante = Estudiante::create([
            'cc' => '23548965',
            'genero' => 'M',
            'telefono' => '987654321',
            'direccion' => 'Cll 7 oeste',
            'contacto_emergencia' => '65495113',
            'observaciones' => 'migrana',
            'user_id' => $user->id,
        ]);
        // Relacionar con los cursos (asumiendo que los cursos ya existen)
        $cursos = Curso::whereIn('id', [2])->get(); // Obtener los cursos con ID 2
        $estudiante->cursos()->attach($cursos); // Crear las relaciones

        $user = User::create([
            'name' => 'ARGEMIRO',
            'lastname' => 'ESCOBAR GUTIERRES',
            'email' => 'argemiro.escobar@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');

        $estudiante = Estudiante::create([
            'cc' => '2354765',
            'genero' => 'M',
            'telefono' => '987654321',
            'direccion' => 'Cll 7 oeste',
            'contacto_emergencia' => '65495113',
            'observaciones' => 'migrana',
            'user_id' => $user->id,
        ]);
        $cursos = Curso::whereIn('id', [3])->get(); // Obtener los cursos con ID 3
        $estudiante->cursos()->attach($cursos); // Crear las relaciones

        $user = User::create([
            'name' => 'Gaspar',
            'lastname' => 'Ijaji',
            'email' => 'gaspar@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');

        $estudiante = Estudiante::create([
            'cc' => '23547657',
            'genero' => 'M',
            'telefono' => '987654321',
            'direccion' => 'Cll 7 oeste',
            'contacto_emergencia' => '65495113',
            'observaciones' => 'migrana',
            'user_id' => $user->id,
        ]);

        $cursos = Curso::whereIn('id', [2])->get(); // Obtener los cursos con ID 1 y 2
        $estudiante->cursos()->attach($cursos); // Crear las relaciones

        $user = User::create([
            'name' => 'Juan David',
            'lastname' => 'Grijalba Osorio',
            'email' => 'juandavidgo1997@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');

        $estudiante = Estudiante::create([
            'cc' => '357986644',
            'genero' => 'M',
            'telefono' => '314756832',
            'direccion' => 'Cll 7 oeste',
            'contacto_emergencia' => '65495113',
            'observaciones' => 'migrana',
            'user_id' => $user->id,
        ]);
        $cursos = Curso::whereIn('id', [1])->get(); // Obtener los cursos con ID 1
        $estudiante->cursos()->attach($cursos); // Crear las relaciones

        // Estudaiante adicional sin curso
        $user = User::create([
            'name' => 'Edward',
            'lastname' => 'Sin Curso',
            'email' => 'sin@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');
        Estudiante::create([
            'cc' => '456789123',
            'genero' => 'M',
            'telefono' => '321654987',
            'direccion' => 'Cll 10 norte',
            'contacto_emergencia' => '65495113',
            'observaciones' => 'Ninguna',
            'user_id' => $user->id,
        ]);

        $user = User::create([
            'name' => 'carmen',
            'lastname' => 'Dos',
            'email' => 'carmen@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');
        Estudiante::create([
            'cc' => '456789124',
            'genero' => 'M',
            'telefono' => '321654988',
            'direccion' => 'Cll 10 norte',
            'contacto_emergencia' => '65495114',
            'observaciones' => 'Ninguna',
            'user_id' => $user->id,
        ]);

        $user = User::create([
            'name' => 'laura',
            'lastname' => 'Dos',
            'email' => 'laura@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');
        Estudiante::create([
            'cc' => '456789125',
            'genero' => 'F',
            'telefono' => '321654989',
            'direccion' => 'Cll 10 norte',
            'contacto_emergencia' => '65495115',
            'observaciones' => 'Ninguna',
            'user_id' => $user->id,
        ]);

        $user = User::create([
            'name' => 'andres',
            'lastname' => 'Dos',
            'email' => 'andres@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');
        Estudiante::create([
            'cc' => '456789126',
            'genero' => 'M',
            'telefono' => '321654990',
            'direccion' => 'Cll 10 norte',
            'contacto_emergencia' => '65495116',
            'observaciones' => 'Ninguna',
            'user_id' => $user->id,
        ]);

        $user = User::create([
            'name' => 'maria',
            'lastname' => 'Dos',
            'email' => 'maria@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123123123'),
        ]);
        $user->assignRole('estudiante');
        Estudiante::create([
            'cc' => '456789127',
            'genero' => 'F',
            'telefono' => '321654991',
            'direccion' => 'Cll 10 norte',
            'contacto_emergencia' => '65495117',
            'observaciones' => 'Ninguna',
            'user_id' => $user->id,
        ]);

        // -------------[ USUARIOS ]----------------]

        //         User::factory()->create([
        //             'name' => 'Test User',
        //             'email' => 'test@email.com',
        //             'password' => bcrypt('123123123'),
        //         ])->assignRole('usuario');

        //         User::factory()->create([
        //             'name' => 'user',
        //             'email' => 'user@email.com',
        //             'password' => bcrypt('123123123'),
        //         ])->assignRole('usuario');

        //         Curso::create([
        //             'nombre' => 'Curso B1',
        //             'descripcion' => 'Curso de conducción para obtener licencia tipo B1.',
        //             'estado' => 'A',
        //         ]);

        //         User::factory(9)->create();
    }
}
