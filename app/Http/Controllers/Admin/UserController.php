<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    // index — paginate 20, filter ?role=, ?status= (active|banned|deleted), ?search=
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin'); // ẩn tài khoản admin
        if ($request->filled('role'))   $query->where('role',   $request->role);
        if ($request->status === 'deleted') $query->onlyTrashed();
        elseif ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where(fn ($q) =>
            $q->where('name',  'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%")
        );
        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    // create — form tạo user mới
    public function create()
    {
        return view('admin.users.create');
    }

    // store — admin tạo trực tiếp, không bắt xác thực email, không cho tạo admin
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', 'in:customer,owner'],
        ]);

        $validated['phone'] = $this->normalizePhone($validated['phone'] ?? null);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Đã tạo tài khoản {$validated['name']}.");
    }

    // show — chi tiết user + thống kê + đơn hàng (paginate 10)
    public function show(User $user)
    {
        $user->loadCount(['bookings', 'venues']);

        $bookings = $user->bookings()->with('court.venue')->latest()->paginate(10);

        return view('admin.users.show', compact('user', 'bookings'));
    }

    // edit — form sửa thông tin
    public function edit(User $user)
    {
        $this->guardAdmin($user);
        return view('admin.users.edit', compact('user'));
    }

    // update — chỉ sửa các trường cho phép; đổi email => phải xác thực lại
    public function update(Request $request, User $user)
    {
        $this->guardAdmin($user);

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user->id],
            'phone'  => ['nullable', 'string', 'max:20'],
            'role'   => ['required', 'in:customer,owner'],
            'status' => ['required', 'in:active,banned'],
        ]);

        $validated['phone'] = $this->normalizePhone($validated['phone'] ?? null);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', "Đã cập nhật thông tin {$user->name}.");
    }

    // updatePassword — reset mật khẩu (không xem mật khẩu hiện tại); cast 'hashed' tự băm
    public function updatePassword(Request $request, User $user)
    {
        $this->guardAdmin($user);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', "Đã đặt lại mật khẩu cho {$user->name}.");
    }

    // destroy — xóa mềm (SoftDeletes); không tự xóa mình, không xóa admin
    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Không thể tự xóa tài khoản của mình.');
        $this->guardAdmin($user);

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Đã xóa tài khoản {$name}.");
    }

    // restore — khôi phục user đã xóa mềm
    public function restore(User $user)
    {
        abort_if($user->role === 'admin', 403);
        $user->restore();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Đã khôi phục tài khoản {$user->name}.");
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

    // Chặn mọi thao tác lên tài khoản admin — admin tự quản hồ sơ qua trang Profile
    private function guardAdmin(User $user): void
    {
        abort_if($user->role === 'admin', 403);
    }

    // Chuẩn hóa SĐT: bỏ khoảng trắng/dấu gạch, +84/84 đầu => 0 (pattern ProfileController)
    private function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/[\s.\-()]/', '', $phone);

        if (str_starts_with($digits, '+84')) {
            $digits = '0' . substr($digits, 3);
        } elseif (str_starts_with($digits, '84') && strlen($digits) === 11) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }
}
