<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    // UC Đăng nhập
    public function login(Request $request)
    {
        // Luồng phụ 1: Bỏ trống thông tin đăng nhập
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập Email hoặc Mã số.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $loginInput = $request->input('email');
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'ma_so';

        // Luồng phụ 2: Email hoặc mật khẩu không chính xác
        if (! Auth::attempt([$fieldType => $loginInput, 'password' => $request->password], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.'])->onlyInput('email');
        }

        $user = Auth::user();

        // Luồng phụ 3: Tài khoản không ở trạng thái hoạt động
        if (! $user->active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khóa hoặc vô hiệu hóa. Vui lòng liên hệ Quản trị viên.']);
        }

        $request->session()->regenerate();

        // Chuyển hướng người dùng đến giao diện phù hợp với vai trò
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'khoa' => redirect()->route('khoa.dashboard'),
            'giangvien' => redirect()->route('giangvien.dashboard'),
            default => redirect()->route('sinhvien.dashboard'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
