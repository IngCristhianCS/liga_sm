<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    
    protected $policies = [
        User::class => UserPolicy::class,
        Equipo::class => EquipoPolicy::class,
        // Agregar otras políticas aquí
    ];

    /**
     * Register any authentication/authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir gates adicionales si es necesario
        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });
    }
}
