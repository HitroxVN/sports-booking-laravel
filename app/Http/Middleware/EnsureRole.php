<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Kiểm tra đăng nhập và role
        if (! $user || ! in_array($user->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // 2. Chặn đứng tài khoản bị khóa (Đã sửa lỗi logic)
        if (isset($user->status) && $user->status === 'banned') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa do vi phạm điều khoản.',
            ]);
        }

        return $next($request);
    }
}