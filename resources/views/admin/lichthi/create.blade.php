@extends('layouts.app')
@section('title', 'Thêm mới kỳ thi & Thiết lập lịch thi')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.lichthi.index') }}" class="text-decoration-none">Quản lý kỳ thi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thêm mới kỳ thi</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-plus-circle text-primary me-2"></i>Thêm mới kỳ thi &amp; Thiết lập ca thi
        </h4>
        <p class="text-muted small mb-0">Thiết lập thông tin kỳ thi, lịch thi theo từng môn học, phân ca thi và phân bổ phòng thi</p>
    </div>
    <a href="{{ route('admin.lichthi.index') }}" class="btn btn-outline-secondary btn-sm px-3" id="btnBack">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
    </a>
</div>

<form method="POST" action="{{ route('admin.lichthi.store') }}" id="createKyThiForm">
    @csrf
    @include('admin.lichthi._form', ['kyThi' => null])
</form>
@endsection


