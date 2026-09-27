@extends('layouts.sinhvien')
@section('title', 'Chứng nhận điện tử')
@section('content')
<div class="mb-3">
    <a href="{{ route('sinhvien.chung-nhan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<div class="card border-0 shadow-lg text-center p-5 bg-white" style="border: 4px double #0d6efd !important;">
    <div class="card-body">
        <div class="mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="HVNH Logo" width="80" height="95">
            <h3 class="fw-bold text-uppercase text-primary mt-3">HỌC VIỆN NGÂN HÀNG</h3>
            <h5 class="text-muted fw-semibold">TRUNG TÂM KHẢO THÍ</h5>
        </div>

        <h2 class="text-danger fw-bold my-4" style="letter-spacing: 2px;">CHỨNG NHẬN HOÀN THÀNH</h2>

        <p class="fs-5 mb-1">Chứng nhận sinh viên:</p>
        <h3 class="fw-bold text-dark mb-3">{{ auth()->user()->name }}</h3>
        <p class="text-muted mb-4">Mã sinh viên: <strong>{{ auth()->user()->ma_so }}</strong></p>

        <p class="fs-5 mb-1">Đã hoàn thành xuất sắc bài thi:</p>
        <h4 class="fw-bold text-primary mb-3">{{ $baithi->deThi->ten_de ?? 'Kỳ thi chứng chỉ' }}</h4>
        <p class="fs-5">Với tổng số điểm: <span class="badge bg-success fs-5 px-3 py-2">{{ $baithi->diem_tong }} / 100</span></p>

        <div class="row mt-5 pt-4 border-top">
            <div class="col-6 text-start">
                <small class="text-muted">Số chứng nhận: <strong>{{ $chungNhan->so_chung_nhan ?? 'Điện tử' }}</strong></small><br>
                <small class="text-muted">Ngày cấp: <strong>{{ $chungNhan->ngay_cap?->format('d/m/Y') ?? now()->format('d/m/Y') }}</strong></small>
            </div>
            <div class="col-6 text-end">
                <p class="fw-bold mb-5">XÁC NHẬN CỦA HỌC VIỆN</p>
                <span class="badge bg-success-subtle text-success border border-success p-2">ĐÃ XÁC THỰC ĐIỆN TỬ</span>
            </div>
        </div>
    </div>
</div>
@endsection
