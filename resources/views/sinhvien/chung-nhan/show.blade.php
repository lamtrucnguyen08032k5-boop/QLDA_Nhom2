@extends('layouts.sinhvien')
@section('title', 'Chứng nhận điện tử trực tuyến')
@section('content')

{{-- Thanh công cụ (ẩn khi in / xuất PDF) --}}
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <a href="{{ route('sinhvien.chung-nhan.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold" onclick="window.print()">
            <i class="bi bi-download me-1"></i> Tải xuống chứng nhận (PDF)
        </button>
    </div>
</div>

{{-- Khung phôi chứng nhận điện tử Học viện Ngân hàng --}}
<div id="print-certificate-area" class="card border-0 shadow-lg text-center p-5 bg-white mx-auto position-relative"
     style="border: 5px double #0d6efd !important; max-width: 900px; border-radius: 12px; background: radial-gradient(circle at center, #ffffff 0%, #fbfdff 100%);">
    
    {{-- Họa tiết góc cổ điển --}}
    <div class="position-absolute top-0 start-0 m-3 text-primary opacity-50" style="font-size: 1.5rem;">✦</div>
    <div class="position-absolute top-0 end-0 m-3 text-primary opacity-50" style="font-size: 1.5rem;">✦</div>
    <div class="position-absolute bottom-0 start-0 m-3 text-primary opacity-50" style="font-size: 1.5rem;">✦</div>
    <div class="position-absolute bottom-0 end-0 m-3 text-primary opacity-50" style="font-size: 1.5rem;">✦</div>

    <div class="card-body py-4">
        {{-- Header chứng nhận --}}
        <div class="mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="HVNH Logo" width="85" height="100" class="mb-2">
            <h4 class="fw-bold text-uppercase text-primary mb-1" style="letter-spacing: 1.5px;">HỌC VIỆN NGÂN HÀNG</h4>
            <h6 class="text-secondary fw-semibold text-uppercase tracking-wider mb-0" style="letter-spacing: 1px;">TRUNG TÂM KHẢO THÍ</h6>
            <div class="mx-auto my-3" style="width: 120px; height: 2px; background: linear-gradient(90deg, transparent, #0d6efd, transparent);"></div>
        </div>

        {{-- Tiêu đề chính --}}
        <h1 class="text-danger fw-bold my-4 text-uppercase" style="letter-spacing: 4px; font-family: 'Times New Roman', serif;">
            GIẤY CHỨNG NHẬN
        </h1>
        <div class="text-muted fst-italic mb-3">Chứng nhận thí sinh:</div>

        {{-- Tên thí sinh --}}
        <h2 class="fw-bold text-dark mb-1 text-uppercase" style="letter-spacing: 1px;">
            {{ auth()->user()->name }}
        </h2>
        <div class="d-flex justify-content-center gap-3 text-muted small mb-4">
            <span>Mã sinh viên: <strong class="font-monospace text-dark">{{ auth()->user()->ma_so }}</strong></span>
            <span>•</span>
            <span>Khóa / Lớp: <strong class="text-dark">{{ auth()->user()->khoa_hoc ?? 'K22' }} - {{ auth()->user()->lop ?? 'K22CLC1' }}</strong></span>
        </div>

        {{-- Nội dung bài thi --}}
        <p class="fs-6 mb-1 text-muted">Đã tham dự và hoàn thành xuất sắc kỳ thi đánh giá năng lực:</p>
        <h4 class="fw-bold text-primary my-2">
            {{ $baithi->deThi->ten_de ?? ($baithi->dangKy?->lichThi?->ten_ky_thi ?? 'Chuẩn kỹ năng Tin học & Ngoại ngữ') }}
        </h4>
        <div class="my-3">
            <span class="fs-6 text-muted me-2">Kết quả đạt được:</span>
            <span class="badge bg-success fs-5 px-3 py-2 shadow-sm border border-success">
                {{ (float)$baithi->diem_tong }} / 100 ĐIỂM
            </span>
        </div>

        {{-- Chữ ký & Xác nhận --}}
        <div class="row mt-5 pt-4 border-top align-items-end">
            <div class="col-6 text-start">
                <div class="small text-muted mb-1">
                    Số chứng nhận: <strong class="font-monospace text-primary fs-6">{{ $chungNhan->so_chung_nhan ?? ('HVNH-E' . date('Y') . '-' . str_pad($baithi->id, 5, '0', STR_PAD_LEFT)) }}</strong>
                </div>
                <div class="small text-muted mb-1">
                    Ngày cấp: <strong>{{ $chungNhan && $chungNhan->ngay_cap ? $chungNhan->ngay_cap->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
                </div>
                <div class="small text-muted">
                    Hình thức: <span class="badge bg-light text-secondary border">Chứng nhận điện tử trực tuyến</span>
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="small text-muted fst-italic mb-2">Hà Nội, ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</div>
                <div class="fw-bold text-uppercase text-dark mb-4">KT. GIÁM ĐỐC TRUNG TÂM KHẢO THÍ</div>
                <div class="d-inline-block p-2 border border-success rounded bg-success-subtle text-success small fw-bold">
                    <i class="bi bi-shield-fill-check me-1"></i>ĐÃ KÝ SỐ ĐIỆN TỬ BỞI HVNH
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
@media print {
    /* Ẩn toàn bộ giao diện web khi in/xuất PDF */
    body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .hvnh-topbar, .hvnh-navbar, footer, .no-print, .btn, nav {
        display: none !important;
    }
    .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #print-certificate-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        max-width: 100% !important;
        box-shadow: none !important;
        border: 4px double #0d6efd !important;
        padding: 30px !important;
        margin: 0 !important;
        page-break-inside: avoid;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    @page {
        size: landscape A4;
        margin: 10mm;
    }
}
</style>
@endsection
