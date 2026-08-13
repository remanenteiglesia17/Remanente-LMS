<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Modulo;
use Illuminate\Database\Seeder;

class ModuloSeeder extends Seeder
{
    /**
     * Crea 3 módulos reales por cada curso existente en BD.
     * Los 2 primeros quedan "finalizado" (curso ya avanzó esas semanas)
     * y el tercero queda abierto (módulo actual, en curso).
     */
    public function run(): void
    {
        $modulosPorCurso = [
            'ANT-2026' => [
                ['nombre' => 'El Pentateuco', 'descripcion' => 'Génesis, Éxodo, Levítico, Números y Deuteronomio: creación, pactos y la Ley.'],
                ['nombre' => 'Libros Históricos y Poéticos', 'descripcion' => 'Josué a Ester, además de Job, Salmos, Proverbios, Eclesiastés y Cantares.'],
                ['nombre' => 'Los Profetas', 'descripcion' => 'Profetas mayores y menores: mensaje, contexto histórico y cumplimiento mesiánico.'],
            ],
            'EV-2026' => [
                ['nombre' => 'Fundamentos del Evangelismo', 'descripcion' => 'Bases bíblicas y teológicas para compartir el evangelio.'],
                ['nombre' => 'Técnicas de Acercamiento', 'descripcion' => 'Métodos prácticos: las 4 Leyes Espirituales, puente romano y evangelismo relacional.'],
                ['nombre' => 'Evangelismo en la Práctica', 'descripcion' => 'Manejo de objeciones, seguimiento del nuevo creyente y trabajo en campo.'],
            ],
            'TEO-2026' => [
                ['nombre' => 'Bibliología', 'descripcion' => 'Inspiración, inerrancia, canon y autoridad de las Escrituras.'],
                ['nombre' => 'Teología Propia', 'descripcion' => 'Existencia, atributos y trinidad de Dios.'],
                ['nombre' => 'Antropología Bíblica', 'descripcion' => 'Origen y naturaleza del ser humano, imagen de Dios y la caída.'],
            ],
        ];

        foreach ($modulosPorCurso as $codigoCurso => $modulos) {
            $curso = Curso::where('codigo', $codigoCurso)->first();

            if (!$curso) {
                continue;
            }

            foreach ($modulos as $index => $data) {
                $orden = $index + 1;

                Modulo::create([
                    'curso_id' => $curso->id,
                    'nombre' => $data['nombre'],
                    'descripcion' => $data['descripcion'],
                    'orden' => $orden,
                    // Los primeros 2 módulos del curso ya se dictaron (finalizados),
                    // el tercero es el módulo actual (abierto / en curso).
                    'finalizado' => $orden < 3,
                    'finalizado_at' => $orden < 3 ? now()->subWeeks(6 - ($orden * 2)) : null,
                ]);
            }
        }
    }
}
