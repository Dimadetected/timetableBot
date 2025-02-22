<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/1366407141:AAEfYjZqot12wg0qVH-PwTl6aJ0movNc-yc/webhook',
        '/1218547211:AAEuYPmA411AR0UdarifUK-4HBB4aFuco5M/webhook',
    ];
}
