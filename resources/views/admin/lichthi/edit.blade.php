@extends('layouts.app')
@section('title', 'Chỉnh sửa kỳ thi: ' . $kyThi->ten_ky_thi)
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.lichthi.index') }}" class="text-decoration-none">Quản lý kỳ thi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa kỳ thi</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-pencil-square text-primary me-2"></i>Chỉnh sửa kỳ thi: <span class="text-primary">{{ $kyThi->ten_ky_thi }}</span>
        </h4>
        <p class="text-muted small mb-0">Cập nhật thông tin kỳ thi, các lịch thi, ca thi và phòng thi được gán</p>
    </div>
    <a href="{{ route('admin.lichthi.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
    </a>
</div>

<form method="POST" action="{{ route('admin.lichthi.update', $kyThi->id) }}" id="editKyThiForm">
    @csrf
    @method('PUT')
    @include('admin.lichthi._form', ['kyThi' => $kyThi])
</form>
@endsection


