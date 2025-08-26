<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request; // <<<--- 1. IMPORT Request

class ServicePolicy
{
    // <<<--- 2. INJECT Request VIA CONSTRUCTOR
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Allow admins to perform any action.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null; // Let other policy methods handle it
    }

    /**
     * Determine whether the user can view any models.
     * Publicly accessible, but only active services.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view service listings
    }

    /**
     * Determine whether the user can view the model.
     * Publicly accessible, but only active services.
     */
    public function view(?User $user, Service $service): bool
    {
        return $service->is_active; // Only active services are viewable
    }

    /**
     * Determine whether the user can create models.
     * Only active sellers with an active package and within their service limit.
     */
    public function create(User $user): Response
    {
        if (!$user->isSeller()) {
            return Response::deny('Only sellers can create services.');
        }
        if (!$user->is_active) {
            return Response::deny('Your seller account is inactive. Please activate it.');
        }

        // <<<--- 3. CRUCIAL ROBUST CHECK HERE
        // Check if seller_package is set, AND if it exists as a key in the config, AND if it's not expired
        if (!$user->seller_package ||
            !config()->has('themabinti.seller_packages.' . $user->seller_package) || // THIS IS THE KEY PART
            $user->package_expiry_date === null ||
            $user->package_expiry_date->isPast()) {
            return Response::deny('You do not have an active seller package. Please upgrade or renew to create services.');
        }

        // Now, we are certain that $user->seller_package is valid and exists in config
        $packageLimits = config('themabinti.seller_packages.' . $user->seller_package);

        // This line will now safely access 'services_limit' because $packageLimits is guaranteed not to be null
        $servicesLimit = $packageLimits['services_limit'];

        if ($servicesLimit !== null && $user->services()->count() >= $servicesLimit) {
            return Response::deny('You have reached your service limit for your current package. Please upgrade your package to add more services.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     * Only the owner of the service can update it.
     */
    public function update(User $user, Service $service): Response
    {
        return $user->id === $service->user_id
            ? Response::allow()
            : Response::deny('You do not own this service.');
    }

    /**
     * Determine whether the user can delete the model.
     * Only the owner of the service can delete it.
     */
    public function delete(User $user, Service $service): Response
    {
        return $user->id === $service->user_id
            ? Response::allow()
            : Response::deny('You do not own this service.');
    }
}