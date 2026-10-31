<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan user yang login memiliki peran yang diizinkan.
 *
 * Penggunaan: Route::middleware('role:dosen') atau Route::middleware('role:mahasiswa')
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Normalkan: 'admin' dianggap setara dengan 'dosen'
        $effectiveRole = ($user && $user->role === 'admin') ? 'dosen' : ($user?->role ?? '');

        if (! $user || ! in_array($effectiveRole, $roles, strict: true)) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak memiliki hak untuk melakukan tindakan ini.',
            ], 403);
        }

        return $next($request);
    }
}
