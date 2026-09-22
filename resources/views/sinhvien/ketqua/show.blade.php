@extends('layouts.sinhvien')
@section('title', 'Chi tiết kết quả thi')
@section('content')

<div class="mb-4">
    <a href="{{ route('sinhvien.ketqua.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách kết quả
    </a>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                Phiếu kết quả thi: {{ $lichThi->ten_ky_thi ?? ($baithi->deThi->ten_de ?? 'Bài thi') }}
            </h4>
            <span class="text-muted small">Mã bài thi: <strong class="font-monospace text-primary">{{ $baithi->ma_bai_thi_hien_thi }}</strong></span>
        </div>

        {{-- Các nút thao tác nghiệp vụ: Phúc khảo & Chứng nhận --}}
        <div class="d-flex flex-wrap gap-2 align-items-center">
            {{-- Xử lý nút Phúc khảo --}}
            @if ($daPhucKhao)
                <a href="{{ route('sinhvien.phuc-khao.index') }}" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-clock-history me-1"></i>Đã gửi yêu cầu phúc khảo
                </a>
            @elseif ($conHanPhucKhao)
                <a href="{{ route('sinhvien.phuc-khao.create', $baithi) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-question-circle me-1"></i>Yêu cầu phúc khảo
                </a>
            @else
                {{-- Luồng phụ 6: Hết thời hạn phúc khảo --}}
                <button type="button" class="btn btn-sm btn-light border text-muted" disabled title="Đã hết thời hạn đăng ký phúc khảo">
                    <i class="bi bi-lock me-1"></i>Đã hết hạn phúc khảo
                </button>
            @endif

            {{-- Xử lý nút Chứng nhận --}}
            @if ($isDat)
                @if ($chungNhan && $chungNhan->trang_thai === 'da_cap')
                    <a href="{{ route('sinhvien.chung-nhan.index') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-award me-1"></i>Xem chứng nhận
                    </a>
                @elseif ($chungNhan && $chungNhan->trang_thai === 'cho_duyet')
                    {{-- Luồng phụ 5: Chứng nhận chưa được cấp xong --}}
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-2">
                        <i class="bi bi-hourglass-split me-1"></i>Chứng nhận: Đang chờ duyệt
                    </span>
                @else
                    <a href="{{ route('sinhvien.chung-nhan.create', $baithi) }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-patch-check me-1"></i>Đăng ký nhận chứng nhận
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Thông báo phụ về hạn phúc khảo nếu sắp hết hoặc đã hết hạn --}}
@if (! $conHanPhucKhao && ! $daPhucKhao)
    <div class="alert alert-secondary small d-flex align-items-center py-2 mb-4">
        <i class="bi bi-info-circle-fill me-2 fs-5 text-muted"></i>
        <div>
            <strong>Đã hết thời hạn đăng ký phúc khảo.</strong> (Thời hạn nhận phúc khảo là 7 ngày kể từ ngày công bố: {{ $hanPhucKhao->format('d/m/Y') }}).
        </div>
    </div>
@elseif ($conHanPhucKhao && ! $daPhucKhao)
    <div class="alert alert-info small d-flex align-items-center py-2 mb-4">
        <i class="bi bi-info-circle-fill me-2 fs-5 text-info"></i>
        <div>
            Thời hạn đăng ký phúc khảo bài thi đến hết ngày <strong>{{ $hanPhucKhao->format('d/m/Y H:i') }}</strong>. Nếu có thắc mắc về điểm thi, vui lòng chọn nút "Yêu cầu phúc khảo" ở trên.
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Cột 1: Thông tin thí sinh & Thông tin ca thi -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-person-vcard me-2 text-primary"></i>Thông tin thí sinh & Phòng thi</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Họ và tên sinh viên</label>
                        <span class="fw-bold text-dark fs-6">{{ $baithi->dangKy?->sinhVien?->name ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Mã sinh viên</label>
                        <span class="font-monospace fw-semibold">{{ $baithi->dangKy?->sinhVien?->ma_so ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Số CCCD / CMND</label>
                        <span class="fw-semibold text-secondary">{{ $baithi->dangKy?->so_cccd ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Lớp / Khóa học</label>
                        <span class="fw-semibold text-secondary">{{ $baithi->dangKy?->sinhVien?->lop ?? '—' }}</span>
                    </div>
                    <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Tên bài thi / Kỳ thi</label>
                        <span class="fw-bold text-dark">{{ $lichThi->ten_ky_thi ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Mã bài thi</label>
                        <span class="font-monospace text-primary fw-bold">{{ $baithi->ma_bai_thi_hien_thi }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Ngày thi</label>
                        <span class="fw-semibold text-dark">{{ optional($lichThi->ngay_thi)->format('d/m/Y') }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Ca thi</label>
                        <span class="badge bg-light text-dark border">{{ $lichThi->ma_ca_thi }}</span>
                        @if($lichThi->gio_bat_dau)
                            <span class="small text-muted ms-1">({{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }})</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Phòng thi</label>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                            <i class="bi bi-door-closed me-1"></i>{{ $lichThi->phong_thi }}
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Địa điểm thi</label>
                        <span class="small text-secondary">Học viện Ngân hàng — 12 Chùa Bộc, Kim Liên, Hà Nội</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột 2: Bảng điểm & Kết quả tổng quan -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-award me-2 text-primary"></i>Kết quả điểm thi</h6>
            </div>
            <div class="card-body pt-0 d-flex flex-column justify-content-between">
                <div>
                    <div class="p-3 bg-light rounded-3 text-center mb-3">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Tổng điểm đạt được</div>
                        <div class="display-5 fw-bold {{ $isDat ? 'text-success' : 'text-danger' }}">
                            {{ $baithi->diem_tong !== null ? (float)$baithi->diem_tong : '—' }}
                        </div>
                        <div class="mt-2">
                            @if ($isDat)
                                <span class="badge bg-success fs-6 px-3 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>ĐẠT
                                </span>
                            @else
                                <span class="badge bg-danger fs-6 px-3 py-1">
                                    <i class="bi bi-x-circle-fill me-1"></i>KHÔNG ĐẠT
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Điểm phần trắc nghiệm:</span>
                        <strong class="text-dark">{{ (float)$baithi->diem_tu_dong }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Điểm phần tự luận:</span>
                        <strong class="text-dark">{{ (float)$baithi->diem_cham_tay }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Ngày công bố kết quả:</span>
                        <span class="text-dark fw-semibold">
                            {{ optional($baithi->ngay_cong_bo)->format('H:i d/m/Y') ?? optional($baithi->created_at)->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Trạng thái cấp chứng chỉ:</span>
                        <span>
                            @if ($chungNhan && $chungNhan->trang_thai === 'da_cap')
                                <span class="badge bg-success">Đã cấp chứng nhận</span>
                            @elseif ($chungNhan && $chungNhan->trang_thai === 'cho_duyet')
                                <span class="badge bg-info text-dark">Đang chờ duyệt</span>
                            @elseif ($isDat)
                                <span class="badge bg-warning-subtle text-warning-emphasis border">Đủ điều kiện cấp</span>
                            @else
                                <span class="badge bg-light text-muted border">Chưa đủ điều kiện</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="mt-3 pt-2 text-center text-muted small border-top">
                    <i class="bi bi-shield-check me-1 text-success"></i>Kết quả đã được xác thực bởi Phòng Khảo thí HVNH
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chi tiết từng câu hỏi trong bài làm (nếu có) -->
@if ($baithi->cauTraLois->isNotEmpty())
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-list-check me-2 text-primary"></i>Chi tiết bài làm từng câu
                <span class="badge bg-light text-secondary border ms-1">{{ $baithi->cauTraLois->count() }} câu</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach ($baithi->cauTraLois as $index => $ctl)
                    @php $ch = $ctl->cauHoi; @endphp
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-bold text-primary">Câu {{ $index + 1 }}:</span>
                            <span class="badge {{ $ctl->diem_dat > 0 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border">
                                Điểm: {{ (float)$ctl->diem_dat }} / {{ $ch ? (float)$ch->diem : '—' }}
                            </span>
                        </div>
                        <div class="mb-2 text-dark">
                            {{ $ch->noi_dung ?? 'Nội dung câu hỏi' }}
                        </div>
                        @if ($ch && $ch->loai_cau === 'tracnghiem')
                            <div class="small bg-light p-2 rounded">
                                <span class="text-muted">Bạn đã chọn đáp án:</span>
                                <strong class="ms-1 font-monospace text-primary">{{ $ctl->dap_an_chon ?? '(Không chọn)' }}</strong>
                            </div>
                        @else
                            <div class="small bg-light p-2 rounded">
                                <span class="text-muted d-block mb-1">Nội dung bài làm tự luận của bạn:</span>
                                <div class="text-dark font-monospace" style="white-space: pre-wrap;">{{ $ctl->bai_lam_tu_luan ?? '(Không có bài làm)' }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@endsection
