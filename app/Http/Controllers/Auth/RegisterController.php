<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SvWhitelist;
use App\Models\User;
use App\Models\EmailVerificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    // UC Đăng ký: bước 1-10 (nhập email trường -> kiểm tra -> gửi link xác minh)
    public function showForm()
    {
        return view('auth.register');
    }

    public function submit(Request $request)
    {
        $input = trim($request->input('email_username') ?? $request->input('email') ?? '');

        if (empty($input)) {
            return back()->withErrors(['email' => 'Vui lòng nhập email trường.'])->withInput();
        }

        // Tự động chuẩn hóa email trường: nếu người dùng chỉ nhập "22A4000001" hoặc nhập đủ "22A4000001@hvnh.edu.vn"
        if (str_contains($input, '@')) {
            $parts = explode('@', $input);
            $email = strtolower(trim($parts[0])) . '@hvnh.edu.vn';
        } else {
            $email = strtolower($input) . '@hvnh.edu.vn';
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(['email' => 'Địa chỉ email không đúng định dạng.'])->withInput();
        }

        $domain = config('app.student_email_domain', '@hvnh.edu.vn');

        // Luồng phụ 1: Email không hợp lệ (không đúng định dạng hoặc không thuộc tên miền trường)
        if (! str_ends_with($email, $domain)) {
            return back()->withErrors(['email' => "Email không hợp lệ hoặc không thuộc tên miền {$domain} của trường."])->withInput();
        }

        // Luồng phụ 2: Email đã được đăng ký
        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'Email này đã được đăng ký tài khoản. Bạn có thể Đăng nhập hoặc chọn Quên mật khẩu.'])->withInput();
        }

        // Kiểm tra whitelist sinh viên (nếu có)
        $sv = SvWhitelist::where('email', $email)->first();

        $token = Str::random(48);
        DB::table('email_verification_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $verifyUrl = route('register.verify', ['token' => $token]);
        $hoTen = $sv ? $sv->ho_ten : 'Sinh viên';

        // Gửi email xác minh (hiệu lực 15 phút)
        Mail::raw("Xin chào {$hoTen},\n\nVui lòng bấm vào liên kết sau để xác minh và hoàn tất đăng ký tài khoản:\n{$verifyUrl}\n\nLiên kết này chỉ có hiệu lực trong vòng 15 phút.", function ($message) use ($email) {
            $message->to($email)->subject('Xác minh tài khoản - Hệ thống thi chứng chỉ HVNH');
        });

        return view('auth.register-sent', ['email' => $email]);
    }

    // UC Đăng ký: bước 11-13 (xác minh liên kết -> hiển thị giao diện thiết lập mật khẩu)
    public function showVerify(string $token)
    {
        $row = DB::table('email_verification_tokens')->where('token', $token)->first();

        // Luồng phụ 3: Liên kết xác minh không hợp lệ hoặc hết hạn
        if (! $row || now()->greaterThan($row->expires_at)) {
            return view('auth.verify-expired');
        }

        return view('auth.set-password', ['token' => $token, 'email' => $row->email]);
    }

    // UC Đăng ký: bước 14-18 (thiết lập mật khẩu -> tạo tài khoản -> thông báo thành công)
    public function completeRegistration(Request $request)
    {
        // Luồng phụ 4: Mật khẩu không hợp lệ
        $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'token.required' => 'Mã xác minh không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $row = DB::table('email_verification_tokens')->where('token', $request->token)->first();

        // Luồng phụ 3: Liên kết xác minh không hợp lệ hoặc hết hạn
        if (! $row || now()->greaterThan($row->expires_at)) {
            return view('auth.verify-expired');
        }

        $sv = SvWhitelist::where('email', $row->email)->first();
        $maSo = $sv ? $sv->ma_sv : explode('@', $row->email)[0];
        $hoTen = $sv ? $sv->ho_ten : 'Sinh viên ' . strtoupper($maSo);
        $lop = $sv ? $sv->lop : null;
        $khoaHoc = $sv ? $sv->khoa_hoc : null;

        User::create([
            'role' => 'sinhvien',
            'ma_so' => $maSo,
            'name' => $hoTen,
            'email' => $row->email,
            'password' => Hash::make($request->password),
            'lop' => $lop,
            'khoa_hoc' => $khoaHoc,
            'email_verified_at' => now(),
        ]);

        if ($sv) {
            $sv->update(['da_dang_ky' => true]);
        }

        DB::table('email_verification_tokens')->where('token', $request->token)->delete();

        // Bước 18: Hệ thống hiển thị thông báo “Đăng ký tài khoản thành công”
        return redirect()->route('login')->with('status', 'Đăng ký tài khoản thành công. Vui lòng đăng nhập vào hệ thống.');
    }
}
