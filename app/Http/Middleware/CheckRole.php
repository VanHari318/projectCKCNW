<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Kiểm tra nếu user đăng nhập và role của user có trong danh sách roles được phép
        if ($request->user() && in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Nếu không có quyền, redirect về trang chủ hoặc hiển thị 403
        abort(403, 'Bạn không có quyền truy cập trang này.');
    }
}
