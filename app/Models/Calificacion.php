<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificacions';

    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'profesor_id',
        'entrega_id',
        'tarea_id',
        'concepto',
        'nota',
        'nota_maxima',
        'tipo_evaluacion',
        'periodo',
        'fecha_calificacion',
        'observaciones',
        'publicada',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
        'nota_maxima' => 'decimal:2',
        'fecha_calificacion' => 'date',
        'publicada' => 'boolean',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Relación con Estudiante
     */
public function estudiante()
{
    // Asegúrate de que la llave foránea sea 'estudiante_id'
    return $this->belongsTo(Estudiante::class, 'estudiante_id');
}

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**  Scope: Solo calificaciones publicadas */
    public function scopePublicadas($query)
    {
        return $query->where('publicada', true);
    }

    /**  Scope: Calificaciones de un período específico */
    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }

    /**  Scope: Calificaciones por tipo de evaluación */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_evaluacion', $tipo);
    }

    /**  Scope: Calificaciones de un curso */  
    public function scopePorCurso($query, $cursoId)
    {
        return $query->where('curso_id', $cursoId);
    }

    /**  Scope: Calificaciones de un estudiante */
    public function scopePorEstudiante($query, $estudianteId)
    {
        return $query->where('estudiante_id', $estudianteId);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**  Obtener la nota en porcentaje  */
    public function getNotaPorcentajeAttribute()
    {
        if ($this->nota_maxima == 0) {
            return 0;
        }
        return round(($this->nota / $this->nota_maxima) * 100, 2);
    }

    /**
     * Aporte de esta calificación a la nota final (0.0 - 5.0), según el
     * sistema de tres niveles (ver promedioPonderadoEstudianteCurso para
     * el detalle completo).
     */
    public function getAporteNotaFinalAttribute()
    {
        $tarea = $this->tarea ?? Tarea::find($this->tarea_id);
        if (!$tarea || !$tarea->modulo_id) {
            return 0;
        }

        $modulo = $tarea->modulo ?? Modulo::find($tarea->modulo_id);
        if (!$modulo) {
            return 0;
        }

        $curso = Curso::find($this->curso_id);
        $totalModulos = $curso ? Modulo::where('curso_id', $curso->id)->count() : 1;
        if ($totalModulos <= 0) {
            return 0;
        }

        $pesosCategoria = $modulo->pesosPorCategoria();
        $sumaPesosActivos = self::sumaPesosCategoriasActivasModulo($modulo->id, $pesosCategoria);
        if ($sumaPesosActivos <= 0) {
            return 0;
        }

        $pesoCategoria = $pesosCategoria[$this->tipo_evaluacion] ?? 0;
        $cantidadMismoTipo = Tarea::where('modulo_id', $modulo->id)
            ->where('tipo', $this->tipo_evaluacion)
            ->count();
        if ($cantidadMismoTipo <= 0) {
            return 0;
        }

        $notaMaxima = $this->nota_maxima ?: 5;

        $aporte = ($this->nota / $notaMaxima)
            * (1 / $cantidadMismoTipo)
            * ($pesoCategoria / $sumaPesosActivos)
            * (1 / $totalModulos)
            * 5;

        return round((float) $aporte, 2);
    }

    /**
     * Verificar si el estudiante aprobó esta evaluación
     */
    public function aprobo($notaMinima = 3.0)
    {
        return $this->nota >= $notaMinima;
    }

    /**
     * Obtener el estado de la calificación (Aprobado/Reprobado)
     */
    public function getEstadoAttribute()
    {
        $notaMinima = 3.0; // Puedes hacerlo configurable
        return $this->nota >= $notaMinima ? 'Aprobado' : 'Reprobado';
    }

    /**
     * Obtener el color para mostrar la calificación
     */
    public function getColorAttribute()
    {
        $porcentaje = $this->nota_porcentaje;

        if ($porcentaje >= 90) return 'success'; // Verde
        if ($porcentaje >= 70) return 'info';    // Azul
        if ($porcentaje >= 60) return 'warning'; // Amarillo
        return 'danger'; // Rojo
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    /**
     * Calcular promedio de calificaciones de un estudiante en un curso
     */
    public static function promedioEstudianteCurso($estudianteId, $cursoId)
    {
        return self::where('estudiante_id', $estudianteId)
            ->where('curso_id', $cursoId)
            ->where('publicada', true)
            ->avg('nota');
    }

    /**
     * Calcular promedio ponderado de un estudiante en un curso.
     *
     * Sistema de tres niveles:
     * 1) Dentro de cada categoría (tarea/quiz/examen/proyecto/foro) DE UN
     *    MÓDULO, todas las actividades del mismo tipo se reparten el peso
     *    de esa categoría en partes iguales (sin pedir un % manual al
     *    crear cada actividad).
     * 2) Cada categoría aporta a la nota del módulo según el % que ese
     *    módulo definió (Modulo::pesosPorCategoria(), debe sumar 100%
     *    entre sus 5 categorías). Igual que antes, si el profesor nunca
     *    creó actividades de alguna categoría dentro del módulo, esa
     *    categoría se excluye y su peso se redistribuye proporcionalmente
     *    entre las categorías que sí tienen actividades en ese módulo.
     * 3) La nota final del curso es el promedio simple de la nota de cada
     *    módulo (se suman los resultados de cada módulo y se divide entre
     *    la cantidad de módulos del curso).
     */
    public static function promedioPonderadoEstudianteCurso($estudianteId, $cursoId)
    {
        $modulos = Modulo::where('curso_id', $cursoId)->get();

        if ($modulos->isEmpty()) {
            return 0;
        }

        $calificaciones = self::where('estudiante_id', $estudianteId)
            ->where('curso_id', $cursoId)
            ->where('publicada', true)
            ->with('tarea')
            ->get()
            ->filter(fn ($c) => $c->tarea && $c->tarea->modulo_id);

        $sumaNotasModulos = 0;

        foreach ($modulos as $modulo) {
            $calificacionesModulo = $calificaciones->filter(
                fn ($c) => $c->tarea->modulo_id === $modulo->id
            );

            $sumaNotasModulos += self::notaModulo($modulo, $calificacionesModulo);
        }

        return round($sumaNotasModulos / $modulos->count(), 2);
    }

    /**
     * Nota (0.0 - 5.0) de un módulo específico, dado el set de
     * calificaciones del estudiante que pertenecen a ese módulo.
     */
    public static function notaModulo(Modulo $modulo, $calificacionesModulo): float
    {
        if ($calificacionesModulo->isEmpty()) {
            return 0;
        }

        $pesosCategoria = $modulo->pesosPorCategoria();
        $sumaPesosActivos = self::sumaPesosCategoriasActivasModulo($modulo->id, $pesosCategoria);

        if ($sumaPesosActivos <= 0) {
            return 0;
        }

        $porCategoria = $calificacionesModulo->groupBy('tipo_evaluacion');

        $notaModulo = 0;

        foreach ($porCategoria as $tipo => $grupo) {
            $pesoCategoria = $pesosCategoria[$tipo] ?? 0;
            if ($pesoCategoria <= 0) {
                continue; // categoría sin ponderación configurada en el módulo
            }

            // Promedio simple de (nota/nota_maxima) entre las actividades de
            // este tipo: equivale a repartir el peso de la categoría en
            // partes iguales entre ellas, sin pedir un % manual por tarea.
            $promedioCategoria = $grupo->avg(function ($calif) {
                $notaMaxima = $calif->nota_maxima ?: 5;
                return $calif->nota / $notaMaxima;
            });

            $notaModulo += $promedioCategoria * $pesoCategoria;
        }

        return ($notaModulo / $sumaPesosActivos) * 5;
    }

    /**
     * Suma de los pesos de categoría (peso_tarea, peso_quiz, ...) que
     * corresponden a categorías con al menos una actividad creada en el
     * módulo. Es el "100%" real sobre el que se escala la nota del
     * módulo, después de redistribuir el peso de las categorías no
     * utilizadas.
     */
    private static function sumaPesosCategoriasActivasModulo($moduloId, array $pesosCategoria): float
    {
        $tiposConActividad = Tarea::where('modulo_id', $moduloId)
            ->distinct()
            ->pluck('tipo');

        $suma = 0;
        foreach ($tiposConActividad as $tipo) {
            $suma += $pesosCategoria[$tipo] ?? 0;
        }

        return (float) $suma;
    }

    public static function estadisticasCurso($cursoId)
    {
        $calificaciones = self::where('curso_id', $cursoId)
            ->where('publicada', true)
            ->get();

        return [
            'total' => $calificaciones->count(),
            'promedio' => round($calificaciones->avg('nota'), 2),
            'nota_maxima' => $calificaciones->max('nota'),
            'nota_minima' => $calificaciones->min('nota'),
            'aprobados' => $calificaciones->filter(fn($c) => $c->nota >= 3.0)->count(),
            'reprobados' => $calificaciones->filter(fn($c) => $c->nota < 3.0)->count(),
        ];
    }
}