<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Chuẩn hóa số điện thoại: bỏ khoảng trắng/dấu gạch và chuyển đầu +84 về 0
        if (! empty($validated['phone'])) {
            $digits = preg_replace('/[\s.\-()]/', '', $validated['phone']);

            if (str_starts_with($digits, '+84')) {
                $digits = '0' . substr($digits, 3);
            } elseif (str_starts_with($digits, '84') && strlen($digits) === 11) {
                $digits = '0' . substr($digits, 2);
            }

            $validated['phone'] = $digits;
        }

        // Ảnh đại diện: lưu ảnh mới theo convention chung (disk public), xóa ảnh cũ
        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar && Storage::disk('public')->exists($request->user()->avatar)) {
                Storage::disk('public')->delete($request->user()->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            // Người dùng chọn "Gỡ ảnh hiện tại" nhưng không tải ảnh mới lên
            if ($request->user()->avatar && Storage::disk('public')->exists($request->user()->avatar)) {
                Storage::disk('public')->delete($request->user()->avatar);
            }

            $validated['avatar'] = null;
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
