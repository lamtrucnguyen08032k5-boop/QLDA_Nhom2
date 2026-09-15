@extends('layouts.guest')
@section('title', 'Đăng nhập')
@section('content')
<div class="mb-3">
    <h5 class="fw-bold text-dark mb-1">Đăng nhập</h5>
    <p class="text-muted small mb-0">Hệ thống quản lý và đăng ký thi chứng chỉ</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Email hoặc Mã tài khoản <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="text" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email hoặc Mã Khoa / Mã GV / Mã SV" required autofocus>
            <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
        </div>
    </div>
    
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label small fw-semibold text-dark mb-0">Mật khẩu <span class="text-danger">*</span></label>
            <a href="{{ route('password.request') }}" class="small text-decoration-none">Quên mật khẩu?</a>
        </div>
        <div class="input-group">
            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    
    <div class="form-check mb-3">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label small text-muted" for="remember">Ghi nhớ đăng nhập</label>
    </div>
    
    <button class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
    </button>
</form>

<div class="text-center mt-3 pt-3 border-top small">
    <span class="text-muted">Chưa có tài khoản?</span>
    <a href="{{ route('register') }}" class="fw-semibold text-decoration-none ms-1">Đăng ký tài khoản Sinh viên</a>
</div>
@endsection
