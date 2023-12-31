<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
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
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('edit-users', function($user)
        {
            return $user->hasRole('admin');
        });

        Gate::define('manage-users', function($user)
        {
            return $user->hasAnyRoles(['admin']);
        });
        
        Gate::define('manage-questions', function($user)
        {
            return $user->hasAnyRoles(['admin', 'moderator']);
        });

        Gate::define('delete-users', function($user)
        {
            return $user->hasRole('admin');
        });
    }
}
