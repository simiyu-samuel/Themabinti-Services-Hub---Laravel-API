<?php

namespace App\Providers;

use App\Models\Service;
use App\Policies\ServicePolicy;
use App\Models\Appointment;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Map your models to their respective policies here
        Service::class => ServicePolicy::class,
        Appointment::class => AppointmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // For Laravel policies, the `boot` method is where you would normally
        // register Gates if you're using them, or define implicit model policies.
        // The `protected $policies` array handles the explicit mapping automatically.
        // The `before` method in your policies (e.g., for admin override) runs
        // before any specific policy methods are called.
    }
}