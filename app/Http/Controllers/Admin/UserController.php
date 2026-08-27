<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // index — paginate 20, filter ?role=, ?status=, ?search=
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin'); // ẩn tài khoản admin
        if ($request->filled('role'))   $query->where('role',   $request->role);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where(fn ($q) =>
            $q->where('name',  'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%")
        );
        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    // ban — guard: không tự ban mình, không ban admin khác, không ban người đã ban
    public function ban(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Không thể tự khóa chính mình.');
        abort_if($user->role === 'admin', 403);
        abort_if($user->status === 'banned', 422, 'Tài khoản này đã bị khóa.');
        $user->update(['status' => 'banned']);
        return back()->with('success', "Đã khóa tài khoản {$user->name}.");
    }

    // unban — guard: không tự mở khóa chính mình (kẻ tấn công admin chỉ bị khóa tay, không tự gỡ)
    public function unban(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Không thể tự mở khóa chính mình.');
        abort_if($user->role === 'admin', 403);
        abort_if($user->status !== 'banned', 422, 'Tài khoản này không bị khóa.');
        $user->update(['status' => 'active']);
        return back()->with('success', "Đã mở khóa tài khoản {$user->name}.");
    }
}
