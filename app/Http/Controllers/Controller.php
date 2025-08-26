<?php

namespace App\Http\Controllers;

// Import the trait
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests; // Also good practice for controllers

// In Laravel 11, the base Controller extends Illuminate\Routing\Controller as BaseController
use Illuminate\Routing\Controller as BaseController; // Ensure this line is present if your default Controller extends it

class Controller extends BaseController // Your base controller likely extends this
{
    // Add the AuthorizesRequests trait here
    use AuthorizesRequests, ValidatesRequests; // Also adding ValidatesRequests for future use

    // No other methods are typically needed here unless you add global controller logic.
}