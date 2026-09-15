@extends('layouts.guest')
@section('title', 'Quên mật khẩu')
@section('content')
<div class="mb-3">
    <h5 class="fw-bold text-dark mb-1">Quên mật khẩu</h5>
    <p class="text-muted small mb-0">
        Nhập email trường hoặc mã số tài khoản của bạn để nhận liên kết đặt lại mật khẩu (hiệu lực 15 phút).
    </p>
</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Email hoặc Tên tài khoản <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="text" name="email" class="form-control" value="{{ old('email') }}" placeholder="Ví dụ: 22a4000001 hoặc admin@hvnh.edu.vn" required autofocus>
            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
        </div>
        <div class="form-text small text-muted mt-1">
            <i class="bi bi-info-circle me-1"></i>Sinh viên có thể nhập mã SV (VD: <code>22A4000001</code>) hoặc email đầy đủ.
        </div>
    </div>

    <button class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-send me-1"></i>Gửi liên kết đặt lại mật khẩu
    </button>
</form>

<div class="text-center mt-3 pt-3 border-top small">
    <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Quay lại trang Đăng nhập
    </a>
</div>
@endsection

