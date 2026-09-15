<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // UC Quên mật khẩu: bước 1-6
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    // UC Quên mật khẩu: bước 7-10
    public function sendResetLink(Request $request)
    {
        $input = trim($request->input('email') ?? '');

        if (empty($input)) {
            return back()->withErrors(['email' => 'Vui lòng nhập email hoặc tài khoản của bạn.'])->withInput();
        }

        // Tự động chuẩn hóa nếu người dùng chỉ nhập mã số/tên người dùng
        $normalizedInput = strtolower($input);
        if (! str_contains($input, '@')) {
            $user = User::where('ma_so', $input)
                ->orWhere('ma_so', strtoupper($input))
                ->orWhere('ma_so', $normalizedInput)
                ->orWhere('email', $normalizedInput . '@hvnh.edu.vn')
                ->first();
        } else {
            $user = User::where('email', $normalizedInput)
                ->orWhere('email', $input)
                ->orWhere('ma_so', explode('@', $input)[0])
                ->first();
        }

        // Luồng phụ 1: Email không tồn tại
        if (! $user) {
            return back()->withErrors(['email' => 'Tài khoản hoặc email không tồn tại trên hệ thống.'])->withInput();
        }

        $token = Str::random(48);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // Gửi email đặt lại mật khẩu
        Mail::raw("Xin chào {$user->name},\n\nBạn vừa yêu cầu đặt lại mật khẩu trên hệ thống thi chứng chỉ HVNH.\nBấm vào liên kết sau để đặt lại mật khẩu (liên kết chỉ có hiệu lực trong vòng 15 phút):\n{$resetUrl}\n\nNếu bạn không yêu cầu, vui lòng bỏ qua email này.", function ($m) use ($user) {
            $m->to($user->email)->subject('Đặt lại mật khẩu - Hệ thống thi chứng chỉ HVNH');
        });

        // Bước 10: Thông báo yêu cầu người dùng kiểm tra email
        return back()->with('status', 'Yêu cầu đặt lại mật khẩu đã được gửi tới email ' . $user->email . '. Vui lòng kiểm tra hộp thư của bạn.');
    }

    // UC Quên mật khẩu: bước 11-13 (kiểm tra liên kết xác minh -> hiển thị giao diện đặt lại mật khẩu)
    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email');
        
        $row = null;
        if ($email) {
            $row = DB::table('password_reset_tokens')->where('email', $email)->first();
        }
        if (! $row) {
            $row = DB::table('password_reset_tokens')->where('token', $token)->first();
        }

        // Luồng phụ 2: Liên kết đặt lại mật khẩu không hợp lệ hoặc hết hạn (15 phút)
        if (! $row || ! ($row->token === $token || Hash::check($token, $row->token)) || $this->isTokenExpired($row->created_at)) {
            return view('auth.reset-expired');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $row->email]);
    }

    // UC Quên mật khẩu: bước 14-18
    public function reset(Request $request)
    {
        // Luồng phụ 3: Mật khẩu mới không hợp lệ & Luồng phụ 4: Mật khẩu xác nhận không khớp
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'token.required' => 'Mã xác minh không hợp lệ.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $row = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (! $row) {
            $row = DB::table('password_reset_tokens')->where('token', $request->token)->first();
        }

        // Luồng phụ 2: Liên kết đặt lại mật khẩu không hợp lệ hoặc hết hạn
        if (! $row || ! ($row->token === $request->token || Hash::check($request->token, $row->token)) || $this->isTokenExpired($row->created_at)) {
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn (chỉ có hiệu lực trong 15 phút).']);
        }

        $user = User::where('email', $row->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Email không tồn tại trên hệ thống.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $row->email)->delete();

        // Bước 18: Hệ thống hiển thị thông báo “Đặt lại mật khẩu thành công”
        return redirect()->route('login')->with('status', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập bằng mật khẩu mới.');
    }

    private function isTokenExpired($createdAt): bool
    {
        if (! $createdAt) {
            return true;
        }

        try {
            $created = Carbon::parse($createdAt);
            // Kiểm tra theo chênh lệch thời gian tuyệt đối (chống lệch timezone UTC / local)
            $diffInMinutes = abs(now()->diffInMinutes($created));
            return $diffInMinutes > 15;
        } catch (\Throwable) {
            return true;
        }
    }
}

