@extends('layouts.sinhvien')
@section('title', 'Chi tiết ca thi')
@section('content')
<div class="card"><div class="card-body">
    <h5>{{ $dangky->lichThi->ten_ky_thi }}</h5>
    <p class="text-muted">Ngày thi: {{ $dangky->lichThi->ngay_thi->format('d/m/Y') }} • Phòng {{ $dangky->lichThi->phong_thi }} • Thời gian: {{ $dangky->lichThi->thoi_gian_thi_phut }} phút</p>

    @if ($dangky->baiThi && $dangky->baiThi->gio_nop)
        @php
            $bt = $dangky->baiThi;
            $maxTN = $bt->tong_diem_toi_da_trac_nghiem;
            $soCauTNDung = $bt->so_cau_trac_nghiem_dung;
            $tongCauTN = $bt->tong_so_cau_trac_nghiem;
            $coTuLuan = $bt->co_tu_luan;
            $maxTL = $bt->tong_diem_toi_da_tu_luan;
            $tongCauTL = $bt->tong_so_cau_tu_luan;
            $daChamTuLuan = $coTuLuan && ($bt->cham_xong || in_array($bt->trang_thai, ['da_chot', 'da_cong_bo']) || $bt->diem_chot !== null);
        @endphp

        <div class="card border-0 shadow-sm rounded-3 mt-3 mb-3 bg-light">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-success mb-0">
                        <i class="bi bi-check-circle-fill me-1"></i> Nộp bài thành công lúc {{ $bt->gio_nop->format('H:i d/m/Y') }}
                    </h6>
                    <small class="text-muted">Mã bài thi: <span class="font-monospace fw-bold text-primary">{{ $bt->ma_bai_thi_hien_thi }}</span></small>
                </div>
                <div>
                    @if (!$coTuLuan)
                        <span class="badge {{ $bt->is_dat ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-1">
                            {{ $bt->is_dat ? 'KẾT QUẢ: ĐẠT' : 'KẾT QUẢ: KHÔNG ĐẠT' }}
                        </span>
                    @else
                        <span class="badge bg-warning text-dark fs-6 px-3 py-1">
                            <i class="bi bi-hourglass-split me-1"></i> Đang chờ chấm tự luận
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">
                    {{-- 1. KẾT QUẢ PHẦN TRẮC NGHIỆM: HIỆN LUÔN ĐIỂM TRÊN THANG ĐIỂM TRẮC NGHIỆM --}}
                    <div class="col-md-6">
                        <div class="card h-100 border border-success border-opacity-25 shadow-none rounded-3">
                            <div class="card-header bg-success bg-opacity-10 py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success small text-uppercase">
                                    <i class="bi bi-ui-checks me-1"></i> Kết quả phần Trắc nghiệm
                                </span>
                                <span class="badge bg-success">Đã chấm tự động</span>
                            </div>
                            <div class="card-body p-3 text-center">
                                <div class="text-muted small mb-1">Điểm trắc nghiệm đạt được</div>
                                <div class="display-6 fw-bold text-success mb-1">
                                    {{ (float) $bt->diem_tu_dong }} <span class="fs-6 text-muted font-monospace">/ {{ $maxTN }} điểm</span>
                                </div>
                                <div class="small text-secondary">
                                    Làm đúng: <strong>{{ $soCauTNDung }}</strong> / {{ $tongCauTN }} câu trắc nghiệm
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. KẾT QUẢ PHẦN TỰ LUẬN: CHƯA CHẤM THÌ CHƯA HIỆN ĐIỂM --}}
                    <div class="col-md-6">
                        <div class="card h-100 border border-secondary border-opacity-25 shadow-none rounded-3">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-secondary small text-uppercase">
                                    <i class="bi bi-pencil-square me-1"></i> Kết quả phần Tự luận
                                </span>
                                @if (!$coTuLuan)
                                    <span class="badge bg-light text-muted border">Không có tự luận</span>
                                @elseif ($daChamTuLuan)
                                    <span class="badge bg-primary">Đã có điểm</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chưa chấm</span>
                                @endif
                            </div>
                            <div class="card-body p-3 text-center">
                                <div class="text-muted small mb-1">Điểm tự luận</div>
                                @if (!$coTuLuan)
                                    <div class="fs-4 fw-bold text-muted my-2">—</div>
                                    <div class="small text-muted">Đề thi 100% trắc nghiệm</div>
                                @elseif ($daChamTuLuan)
                                    <div class="display-6 fw-bold text-primary mb-1">
                                        {{ (float) ($bt->diem_chot ?? $bt->diem_cham_tay) }} <span class="fs-6 text-muted font-monospace">/ {{ $maxTL }} điểm</span>
                                    </div>
                                    <div class="small text-secondary">{{ $tongCauTL }} câu tự luận</div>
                                @else
                                    <div class="fs-5 fw-bold text-warning-emphasis my-2">
                                        <i class="bi bi-clock-history me-1"></i> Chưa chấm
                                    </div>
                                    <div class="small text-muted">
                                        {{ $tongCauTL }} câu tự luận (Thang điểm: {{ $maxTL }}đ) · Đang chờ Giảng viên chấm thi
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 3. TỔNG ĐIỂM TOÀN BÀI --}}
                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-white border d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="text-muted small d-block">Tổng điểm toàn bài thi:</span>
                                @if ($bt->diem_tong !== null)
                                    <span class="fs-4 fw-bold {{ $bt->is_dat ? 'text-success' : 'text-danger' }}">
                                        {{ (float) $bt->diem_tong }} / 100 điểm
                                    </span>
                                    <span class="badge {{ $bt->is_dat ? 'bg-success' : 'bg-danger' }} ms-2">
                                        {{ $bt->is_dat ? 'ĐẠT' : 'KHÔNG ĐẠT' }}
                                    </span>
                                @else
                                    <span class="fs-5 fw-bold text-secondary">
                                        Chờ tổng hợp sau khi có điểm tự luận (Thang điểm toàn bài: 100 điểm)
                                    </span>
                                @endif
                            </div>

                            @if ($bt->trang_thai === 'da_cong_bo' || $bt->diem_tong !== null)
                                <a href="{{ route('sinhvien.ketqua.show', $bt) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-earmark-text me-1"></i> Xem chi tiết kết quả &amp; Phúc khảo
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($dangky->lichThi->trang_thai !== 'dang_thi')
        <div class="alert alert-warning">Ca thi chưa bắt đầu. Vui lòng quay lại đúng giờ thi.</div>
    @else
        <form method="POST" action="{{ route('sinhvien.thi.nhap-ma', $dangky) }}" class="row g-2">
            @csrf
            <div class="col-md-4">
                <input name="ma_ca_thi" class="form-control" placeholder="Nhập mã ca thi" required>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary">Vào phòng thi</button>
            </div>
        </form>
    @endif
</div></div>
@endsection
