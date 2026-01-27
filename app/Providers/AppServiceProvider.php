<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Inventario;
use App\Observers\InventarioObserver;
use App\Models\AuditLog;
use App\Observers\AuditableObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }

            // Permite usar `can:rol` en AdminLTE (can => 'admin', 'limited_user', etc.)
            if (method_exists($user, 'hasRole') && $user->hasRole($ability)) {
                return true;
            }

            // Permite usar permisos como abilities (inventario_limited, ventas_limited, etc.)
            if (method_exists($user, 'hasPermissionTo')) {
                try {
                    if ($user->hasPermissionTo($ability)) {
                        return true;
                    }
                } catch (Throwable $e) {
                    // Si el permiso no existe u otro error, dejamos que Gate continúe.
                }
            }

            return null;
        });

        $this->registerAuditObservers();

        Inventario::observe(InventarioObserver::class);
    }

    protected function registerAuditObservers(): void
    {
        $observer = AuditableObserver::class;

        // Descubre modelos en app/Models y registra un observer global de auditoría.
        $modelFiles = File::glob(app_path('Models') . '/*.php') ?: [];

        foreach ($modelFiles as $file) {
            $class = 'App\\Models\\' . pathinfo($file, PATHINFO_FILENAME);

            if (! class_exists($class)) {
                continue;
            }

            if (! is_subclass_of($class, Model::class)) {
                continue;
            }

            if ($class === AuditLog::class) {
                continue;
            }

            $class::observe($observer);
        }
    }
}
