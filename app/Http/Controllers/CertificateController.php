<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Config; 
use App\Models\Curso;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

     // 3. Buscar la configuración y resolver la ruta del logo dinámicamente
        $config = Config::first();
        $imageUrl = $config?->image?->url;

        if ($imageUrl) {
            $relativeUrl = ltrim($imageUrl, '/');
            if (str_starts_with($relativeUrl, 'storage/')) {
                $relativeUrl = substr($relativeUrl, 8);
            }

            $fullLogoPath = public_path('storage/' . $relativeUrl);
        } else {
            $fullLogoPath = null;
        }

        // 4. Logo fallback en caso de que no exista el personalizado
        $defaultLogo = public_path('vendor/adminlte/dist/img/hatLogo.png');

        $logoPath = ($fullLogoPath && file_exists($fullLogoPath))
            ? $fullLogoPath
            : (file_exists($defaultLogo) ? $defaultLogo : null);

        // 5. Formatear la fecha traducida al español
        $issueDate = Carbon::parse($certificate->issued_at)
            ->locale('es')
            ->translatedFormat('d \d\e F \d\e Y');

        // 6. Datos para la plantilla PDF
        $data = [
            'student_name'     => $user->name,
            'course_title'     => $course->nombre,
            'course_code'      => $course->codigo ?? '',
            'issue_date'       => $issueDate,
            'certificate_code' => $certificate->code,
            'platform_name'    => config('app.name', 'Canvas Church'),
            'logo_path'        => $logoPath,
        ];

        // 7. Renderizar y descargar el PDF
        $pdf = Pdf::loadView('certificates.template', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download("Certificado-{$course->codigo}-{$user->id}.pdf");
    }
}