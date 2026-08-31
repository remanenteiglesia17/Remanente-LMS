<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
            // Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Gate exclusivo para mostrar/ocultar la sección "ESTUDIANTE" del
         * sidebar. A diferencia del permiso 'estudiante.cursos.index' (que
         * todo estudiante tiene por rol), este exige ADEMÁS que el
         * estudiante tenga al menos un curso asignado en 'estudiante_curso'.
         *
         * Importante: el nombre NO debe coincidir con ningún registro de la
         * tabla 'permissions'. Spatie intercepta cada Gate::check() con su
         * propio Gate::before(); si el ability existe como permiso, responde
         * él mismo y este Gate::define() de abajo ni se ejecuta. Al usar un
         * nombre distinto, Spatie no lo reconoce, deja pasar la evaluación,
         * y entra nuestra lógica.
         */
        Gate::define('estudiante.menu-cursos', function (User $user) {
            if (!$user->hasPermissionTo('estudiante.cursos.index')) {
                return false;
            }

            $estudiante = $user->estudiante;

            return (bool) ($estudiante && $estudiante->cursos()->exists());
        });
    }
}
