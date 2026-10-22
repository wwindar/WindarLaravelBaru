<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Admin-Token');
        $expectedToken = config('app.admin_api_token', env('ADMIN_API_TOKEN'));

        if (empty($token) || empty($expectedToken) || ! hash_equals((string) $expectedToken, (string) $token)) {
            return response()->json([
                'message' => 'Token admin tidak valid atau tidak disertakan.',
            ], 401);
        }

        return $next($request);
    }
}
