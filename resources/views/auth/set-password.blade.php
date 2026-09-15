@extends('layouts.guest')
@section('title', 'Thiết lập mật khẩu')
@section('content')
<div class="mb-3">
    <div class="d-flex align-items-center mb-2">
        <span class="badge bg-success bg-opacity-10 text-success p-2 rounded-circle me-2">
            <i class="bi bi-check-circle fs-5"></i>
        </span>
        <h5 class="fw-bold text-dark mb-0">Xác minh email thành công</h5>
    </div>
    <p class="text-muted small mb-0">
        Vui lòng tạo mật khẩu cho tài khoản <strong>{{ $email }}</strong> để hoàn tất đăng ký.
    </p>
</div>

<form method="POST" action="{{ route('register.complete') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
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
        <label class="form-label small fw-semibold text-dark">Xác nhận mật khẩu <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="password" name="password_confirmation" class="form-control" minlength="8" placeholder="Nhập lại mật khẩu mới" required>
            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    <button class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-check2-circle me-1"></i>Hoàn tất đăng ký
    </button>
</form>
@endsection
