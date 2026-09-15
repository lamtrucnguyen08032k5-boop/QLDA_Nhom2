@extends('layouts.guest')
@section('title', 'Kiểm tra email xác minh')
@section('content')
<div class="text-center py-2">
    <div class="mb-3">
        <span class="badge rounded-circle p-3 bg-success bg-opacity-10 text-success" style="font-size: 2.2rem;">
            <i class="bi bi-envelope-check"></i>
        </span>
    </div>
    <h5 class="fw-bold text-dark mb-2">Kiểm tra hộp thư của bạn</h5>
    <p class="text-muted small mb-3">
        Hệ thống đã gửi liên kết xác minh tới email: <br>
        <strong class="text-primary fs-6">{{ $email }}</strong>
    </p>
    <div class="alert alert-warning py-2 small mb-3 text-start border-0 bg-warning bg-opacity-10 text-dark">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-warning fs-5"></i>
            <div>
                <strong>Lưu ý:</strong> Liên kết xác minh chỉ có hiệu lực trong vòng <strong>15 phút</strong>. Vui lòng kiểm tra cả thư mục Spam/Rác nếu không thấy thư.
            </div>
        </div>
    </div>
    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
        <i class="bi bi-arrow-left me-1"></i>Quay lại trang Đăng nhập
    </a>
</div>
@endsection

