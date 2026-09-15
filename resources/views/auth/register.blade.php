@extends('layouts.guest')
@section('title', 'Đăng ký tài khoản Sinh viên')
@section('content')
<div class="mb-3">
    <h5 class="fw-bold text-dark mb-1">Đăng ký tài khoản Sinh viên</h5>
    <p class="text-muted small mb-0">
        Vui lòng nhập tên người dùng hoặc mã sinh viên được Học viện cấp. Hệ thống sẽ kiểm tra và gửi liên kết xác minh (hiệu lực 15 phút) tới email của bạn.
    </p>
</div>

<form method="POST" action="{{ route('register.submit') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Email trường <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="text" name="email_username" class="form-control" value="{{ old('email_username') }}" placeholder="Ví dụ: 22a4000001" required autofocus>
            <span class="input-group-text bg-light text-primary fw-bold">@hvnh.edu.vn</span>
        </div>
        <div class="form-text small text-muted mt-1">
            <i class="bi bi-info-circle me-1"></i>Chỉ cần nhập phần trước của email (VD: <code>22a4000001</code>).
        </div>
    </div>

    <button class="btn btn-primary w-100 py-2 fw-semibold">
        Đăng ký / Tiếp tục <i class="bi bi-arrow-right ms-1"></i>
    </button>
</form>

<div class="text-center mt-3 pt-3 border-top small">
    <span class="text-muted">Đã có tài khoản?</span>
    <a href="{{ route('login') }}" class="fw-semibold text-decoration-none ms-1">Đăng nhập</a>
</div>
@endsection

