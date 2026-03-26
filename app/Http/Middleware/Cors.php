<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Orígenes permitidos para CORS.
     * Las apps móviles nativas NO son afectadas por CORS.
     * Agregar aquí solo dominios web que necesiten acceder al API.
     */
    protected $allowedOrigins = [
        'http://18.225.57.224',
        'https://18.225.57.224',
        'http://localhost',
        'http://localhost:8000',
        'http://127.0.0.1',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $origin = $request->header('Origin', '');
        
        // Solo permitir orígenes conocidos
        $allowedOrigin = in_array($origin, $this->allowedOrigins) ? $origin : '';

        // Handle preflight OPTIONS request immediately
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization, Accept, Origin')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        $response = $next($request);

        if (!empty($allowedOrigin)) {
            $response->header('Access-Control-Allow-Origin', $allowedOrigin)
                     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                     ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization, Accept, Origin')
                     ->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
