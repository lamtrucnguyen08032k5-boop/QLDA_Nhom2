@extends('layouts.app')
@section('title', 'Chi tiết bài làm: ' . $baithi->ma_bai_thi_hien_thi)
@section('content')

<div class="mb-3">
    @if ($lichThi)
        <a href="{{ route('admin.ketqua.show', $lichThi) }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i>Quay lại phòng thi {{ $lichThi->phong_thi }}
        </a>
    @else
        <a href="{{ route('admin.ketqua.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
        </a>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="text-primary fw-bold mb-1">
                <i class="bi bi-file-earmark-text me-2"></i>Chi tiết bài làm:
                <span class="font-monospace badge bg-primary fs-6 ms-1">{{ $baithi->ma_bai_thi_hien_thi }}</span>
            </h4>
            <div class="text-muted small">
                <span><i class="bi bi-person me-1"></i>Sinh viên: <strong>{{ $sinhVien?->name ?? '—' }}</strong> ({{ $sinhVien?->ma_so ?? '—' }})</span>
                @if ($lichThi)
                    <span class="mx-2">•</span>
                    <span><i class="bi bi-door-closed me-1"></i>Phòng: <strong>{{ $lichThi->phong_thi }}</strong></span>
                    <span class="mx-2">•</span>
                    <span><i class="bi bi-clock me-1"></i>Ca: <strong>{{ $lichThi->ma_ca_thi }}</strong></span>
                @endif
            </div>
        </div>
        <div>
            @if ($baithi->is_dat)
                <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>ĐẠT</span>
            @elseif ($baithi->diem_tong !== null)
                <span class="badge bg-danger fs-6 px-3 py-2"><i class="bi bi-x-circle-fill me-1"></i>KHÔNG ĐẠT</span>
            @else
                <span class="badge bg-secondary fs-6 px-3 py-2">Chưa có điểm</span>
            @endif
        </div>
    </div>
</div>

{{-- Thông tin tổng quan --}}
<div class="row g-3 mb-4">
    {{-- Cột 1: Thông tin sinh viên & ca thi --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-person-vcard me-2 text-primary"></i>Thông tin thí sinh</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Họ và tên</label>
                        <span class="fw-bold text-dark">{{ $sinhVien?->name ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Mã sinh viên</label>
                        <span class="font-monospace fw-semibold">{{ $sinhVien?->ma_so ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Số CCCD / CMND</label>
                        <span class="fw-semibold text-secondary">{{ $baithi->dangKy?->so_cccd ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Email</label>
                        <span class="small text-secondary">{{ $sinhVien?->email ?? '—' }}</span>
                    </div>
                    <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Ngày thi</label>
                        <span class="fw-semibold">{{ optional($lichThi?->ngay_thi)->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Ca thi / Phòng</label>
                        <span class="badge bg-light text-dark border me-1">{{ $lichThi?->ma_ca_thi ?? '—' }}</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $lichThi?->phong_thi ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Bắt đầu làm bài</label>
                        <span class="text-secondary small">{{ optional($baithi->gio_bat_dau)->format('H:i d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Nộp bài lúc</label>
                        <span class="text-secondary small">{{ optional($baithi->gio_nop)->format('H:i d/m/Y') ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cột 2: Kết quả điểm & chấm bài --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-award me-2 text-primary"></i>Kết quả điểm & thông tin chấm</h6>
            </div>
            <div class="card-body pt-0">
                {{-- Điểm tổng to --}}
                <div class="p-3 bg-light rounded-3 text-center mb-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Tổng điểm đạt được</div>
                    <div class="display-5 fw-bold {{ $baithi->is_dat ? 'text-success' : ($baithi->diem_tong !== null ? 'text-danger' : 'text-secondary') }}">
                        {{ $baithi->diem_tong !== null ? (float)$baithi->diem_tong : '—' }}
                    </div>
                </div>

                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Điểm phần trắc nghiệm (tự động):</span>
                    <strong class="text-dark">{{ $baithi->diem_tu_dong !== null ? (float)$baithi->diem_tu_dong : '—' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Điểm phần tự luận (chấm tay):</span>
                    <strong class="text-dark">{{ $baithi->diem_cham_tay !== null ? (float)$baithi->diem_cham_tay : '—' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Người chấm bài:</span>
                    <span class="fw-semibold text-dark">
                        @if ($baithi->giangVien)
                            <i class="bi bi-person-check me-1 text-success"></i>{{ $baithi->giangVien->name }}
                        @elseif ($baithi->cham_xong)
                            <span class="text-muted small"><i class="bi bi-robot me-1"></i>Chấm tự động</span>
                        @else
                            <span class="text-warning small"><i class="bi bi-hourglass me-1"></i>Chưa có người chấm</span>
                        @endif
                    </span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Ngày chấm xong:</span>
                    <span class="text-secondary small">{{ optional($baithi->ngay_cham)->format('H:i d/m/Y') ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Ngày công bố kết quả:</span>
                    <span class="text-secondary small">
                        @if ($baithi->ngay_cong_bo)
                            {{ $baithi->ngay_cong_bo->format('H:i d/m/Y') }}
                            <br><small class="text-muted">bởi {{ optional($baithi->nguoiCongBo)->name ?? 'Admin' }}</small>
                        @else
                            <span class="text-warning">Chưa công bố</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chi tiết từng câu hỏi --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0">
            <i class="bi bi-list-check me-2 text-primary"></i>Chi tiết bài làm từng câu
            <span class="badge bg-light text-secondary border ms-1">{{ $baithi->cauTraLois->count() }} câu</span>
        </h6>
        @if ($baithi->deThi)
            <span class="small text-muted">Đề: <strong>{{ $baithi->deThi->ten_de }}</strong></span>
        @endif
    </div>
    <div class="card-body p-0">
        @if ($baithi->cauTraLois->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-2 d-block mb-2 text-secondary opacity-50"></i>
                <div>Chưa có dữ liệu bài làm từng câu.</div>
                <small>Sinh viên chưa nộp bài hoặc bài làm không được ghi nhận chi tiết.</small>
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach ($baithi->cauTraLois as $index => $ctl)
                    @php $ch = $ctl->cauHoi; @endphp
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-bold text-primary">Câu {{ $index + 1 }}
                                @if ($ch)
                                    <span class="badge bg-light text-muted border ms-1" style="font-size:0.7rem;">
                                        {{ $ch->loai_cau === 'tracnghiem' ? 'Trắc nghiệm' : 'Tự luận' }}
                                    </span>
                                @endif
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                @if ($ctl->da_cham || ($ch && $ch->loai_cau === 'tracnghiem'))
                                    <span class="badge {{ (float)$ctl->diem_dat > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2 py-1">
                                        <i class="bi bi-check2 me-1"></i>Điểm: {{ (float)$ctl->diem_dat }} / {{ $ch ? (float)$ch->diem : '—' }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-hourglass me-1"></i>Chưa chấm — {{ $ch ? (float)$ch->diem : '—' }} điểm
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 text-dark small">{{ $ch?->noi_dung ?? '(Không có nội dung câu hỏi)' }}</div>
                        @if ($ch && $ch->loai_cau === 'tracnghiem')
                            <div class="d-flex flex-wrap gap-3 bg-light p-2 rounded small">
                                @foreach (['A', 'B', 'C', 'D'] as $opt)
                                    @php
                                        $field = 'dap_an_' . strtolower($opt);
                                        $val = $ch->$field ?? null;
                                        $isCorrect = strtoupper($ch->dap_an_dung ?? '') === $opt;
                                        $isChosen  = strtoupper($ctl->dap_an_chon ?? '') === $opt;
                                    @endphp
                                    @if ($val)
                                        <div class="px-2 py-1 rounded
                                            {{ $isCorrect && $isChosen ? 'bg-success text-white fw-bold' : '' }}
                                            {{ $isCorrect && !$isChosen ? 'border border-success text-success' : '' }}
                                            {{ !$isCorrect && $isChosen ? 'bg-danger text-white' : '' }}
                                            {{ !$isCorrect && !$isChosen ? 'text-muted' : '' }}">
                                            <strong>{{ $opt }}.</strong> {{ $val }}
                                            @if ($isChosen) <i class="bi bi-cursor-fill ms-1 small"></i> @endif
                                            @if ($isCorrect) <i class="bi bi-check-circle ms-1 small"></i> @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="small mt-1 text-muted">
                                Sinh viên chọn: <strong class="font-monospace text-{{ (float)$ctl->diem_dat > 0 ? 'success' : 'danger' }}">{{ $ctl->dap_an_chon ?? '(Không chọn)' }}</strong>
                                — Đáp án đúng: <strong class="font-monospace text-success">{{ strtoupper($ch->dap_an_dung ?? '—') }}</strong>
                            </div>
                        @else
                            <div class="bg-light p-2 rounded small">
                                <div class="text-muted mb-1 fw-semibold">Bài làm tự luận của sinh viên:</div>
                                <div class="text-dark font-monospace" style="white-space: pre-wrap; max-height: 200px; overflow-y: auto;">{{ $ctl->bai_lam_tu_luan ?? '(Không có nội dung)' }}</div>
                            </div>
                            @if ($ctl->da_cham)
                                <div class="small mt-1 text-success">
                                    <i class="bi bi-person-check me-1"></i>Đã chấm — Điểm được: <strong>{{ (float)$ctl->diem_dat }}</strong> / {{ $ch ? (float)$ch->diem : '—' }}
                                </div>
                            @else
                                <div class="small mt-1 text-warning">
                                    <i class="bi bi-hourglass me-1"></i>Câu tự luận này chưa được chấm điểm.
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
