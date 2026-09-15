@extends('layouts.guest')
@section('title', 'Liên kết hết hạn')
@section('content')
<div class="text-center py-2">
    <div class="mb-3">
        <span class="badge rounded-circle p-3 bg-danger bg-opacity-10 text-danger" style="font-size: 2.2rem;">
            <i class="bi bi-shield-exclamation"></i>
        </span>
    </div>
    <h5 class="fw-bold text-danger mb-2">Liên kết đặt lại mật khẩu đã hết hạn</h5>
    <p class="text-muted small mb-3">
        Liên kết đặt lại mật khẩu của bạn đã hết hiệu lực (chỉ có giá trị trong <strong>15 phút</strong>) hoặc không hợp lệ. Vui lòng gửi lại yêu cầu mới.
    </p>
    <div class="d-grid gap-2">
        <a href="{{ route('password.request') }}" class="btn btn-primary">
            <i class="bi bi-arrow-repeat me-1"></i>Yêu cầu đặt lại mật khẩu mới
        </a>
        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
            Quay lại Đăng nhập
        </a>
    </div>
</div>
@endsection
