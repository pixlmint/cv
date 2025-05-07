<?php

namespace App\Hook;

use App\Controller\NotFoundController;
use Nacho\Contracts\Hooks\OnRouteNotFoundFunction;
use Nacho\Models\Route;

class RouteNotFoundHook implements OnRouteNotFoundFunction
{
    public function call(string $path): Route
    {
        return new Route([
            'route' => $path,
            'controller' => NotFoundController::class,
            'function' => 'notFound',
        ]);
    }
}
