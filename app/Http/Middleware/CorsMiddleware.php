<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Handle preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200, [
                'Access-Control-Allow-Origin' => $request->header('Origin') ?? '*',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS, PATCH',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age' => '86400',
            ]);
        }

        // For non-preflight requests, add the CORS headers to the response
        $response = $next($request);

        // Don't add headers to StreamedResponse - they're already set in the controller
        if (!($response instanceof StreamedResponse)) {
            $response->header('Access-Control-Allow-Origin', $request->header('Origin') ?? '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
