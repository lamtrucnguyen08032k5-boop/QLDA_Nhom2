@extends('layouts.guest')
@section('title', 'Đặt lại mật khẩu')
@section('content')
<div class="mb-3">
    <h5 class="fw-bold text-dark mb-1">Đặt lại mật khẩu</h5>
    <p class="text-muted small mb-0">Vui lòng nhập mật khẩu mới cho tài khoản <strong>{{ $email }}</strong>.</p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">
    
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Email tài khoản</label>
        <div class="input-group">
            <input type="email" class="form-control bg-light" value="{{ $email }}" disabled>
            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Mật khẩu mới <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="password" name="password" class="form-control" minlength="8" placeholder="Tối thiểu 8 ký tự" required autofocus>
            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="password" name="password_confirmation" class="form-control" minlength="8" placeholder="Nhập lại mật khẩu mới" required>
            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    
    <button class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-arrow-repeat me-1"></i>Đặt lại mật khẩu
    </button>
</form>

<div class="text-center mt-3 pt-3 border-top small">
    <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Quay lại trang Đăng nhập
    </a>
</div>
@endsection
