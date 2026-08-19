<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Curso;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function generate(Curso $course)
    {
        $user = auth()->user();

        // Verificar que el curso pertenece al estudiante y que lo completó
        if (!$user->hasCompletedCourse($course)) {
            return redirect()->back()->with([
                'toast' => 2,
                'title' => 'No disponible',
                'info'  => 'Aún no has completado este curso. Debes aprobar con nota ≥ 3.0 y cumplir las horas de asistencia.',
                'icon'  => 'warning',
            ]);
        }

        // Obtener o crear el certificado (código único por estudiante + curso)
        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'curso_id' => $course->id],
            [
                'code'      => 'CC-' . strtoupper(Str::random(8)),
                'issued_at' => now(),
            ]
        );

        // Datos para la plantilla PDF
        $data = [
            'student_name'     => $user->name,
            'course_title'     => $course->nombre,
            'course_code'      => $course->codigo ?? '',
            'issue_date'       => $certificate->issued_at->format('d \d\e F \d\e Y'),
            'certificate_code' => $certificate->code,
            'platform_name'    => config('app.name', 'Canvas Church'),
        ];

        $pdf = Pdf::loadView('certificates.template', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download("Certificado-{$course->codigo}-{$user->id}.pdf");
    }
}
