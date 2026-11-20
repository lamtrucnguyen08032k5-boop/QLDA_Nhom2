@extends('layouts.app')
@section('title', 'Thống nhất điểm chấm bài thi')
@section('content')
<div class="mb-3">
    <a href="{{ route('giangvien.cham-thi.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-exclamation-circle-fill fs-4 me-3"></i>
    <div>
        <strong>Phát hiện chênh lệch điểm tự luận:</strong> Kết quả chấm tự luận giữa Giảng viên 1 và Giảng viên 2 có chênh lệch cần thống nhất.
        Hai Giảng viên tiến hành trao đổi, Giảng viên 2 nhập tổng điểm đã thống nhất và Giảng viên 1 bấm xác nhận để hoàn tất chốt điểm.
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="mb-1"><strong>Sinh viên:</strong> {{ $baithi->dangKy->sinhVien->name ?? 'N/A' }} ({{ $baithi->dangKy->sinhVien->ma_so ?? '' }})</p>
                <p class="mb-0"><strong>Kỳ thi:</strong> {{ $baithi->dangKy->lichThi->ten_ky_thi ?? 'N/A' }} • <strong>Đề thi:</strong> {{ $baithi->deThi->ten_de ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>GV1 chấm:</strong> <span class="fw-bold text-primary">{{ $baithi->giangVien1->name ?? 'N/A' }}</span></p>
                <p class="mb-0"><strong>GV2 chấm:</strong> <span class="fw-bold text-info">{{ $baithi->giangVien2->name ?? 'N/A' }}</span></p>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('giangvien.cham-thi.luu-thong-nhat', $baithi->id) }}">
    @csrf

    {{-- BẢNG ĐỐI CHIẾU TỔNG ĐIỂM TỰ LUẬN GV1 VS GV2 --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title fw-bold mb-0">Đối chiếu tổng điểm tự luận &amp; Nhập điểm thống nhất</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4 align-items-center mb-4">
                <div class="col-md-4">
                    <div class="p-3 bg-primary-subtle rounded border border-primary text-center">
                        <small class="text-primary fw-bold text-uppercase d-block mb-1">Điểm tự luận GV1 chấm</small>
                        <span class="fs-2 fw-bold text-primary">{{ (float)$baithi->diem_tu_luan_gv1 }}</span>
                        <span class="text-muted small">/ {{ $tongDiemToiDaTuLuan }} điểm</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-info-subtle rounded border border-info text-center">
                        <small class="text-info-emphasis fw-bold text-uppercase d-block mb-1">Điểm tự luận GV2 chấm</small>
                        <span class="fs-2 fw-bold text-info">{{ (float)$baithi->diem_tu_luan_gv2 }}</span>
                        <span class="text-muted small">/ {{ $tongDiemToiDaTuLuan }} điểm</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-warning-subtle rounded border border-warning text-center">
                        <small class="text-warning-emphasis fw-bold text-uppercase d-block mb-1">Độ lệch điểm chấm</small>
                        <span class="fs-2 fw-bold text-danger">{{ abs((float)$baithi->diem_tu_luan_gv1 - (float)$baithi->diem_tu_luan_gv2) }}</span>
                        <span class="text-muted small">điểm</span>
                    </div>
                </div>
            </div>

            {{-- KHU VỰC NHẬP ĐIỂM THỐNG NHẤT --}}
            <div class="bg-light p-4 rounded-3 border mb-3">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-check2-circle text-success me-2"></i>Điểm tự luận sau khi hai GV thống nhất</h6>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Tổng điểm tự luận thống nhất <span class="text-danger">*</span></label>
                        @if($isGV2)
                            <div class="input-group input-group-lg">
                                <input type="number" step="0.1" min="0" max="{{ $tongDiemToiDaTuLuan }}"
                                       name="diem_tu_luan_thong_nhat"
                                       class="form-control fw-bold fs-4 text-success border-success"
                                       value="{{ old('diem_tu_luan_thong_nhat', $baithi->diem_chot ?? $baithi->diem_tu_luan_gv2) }}"
                                       required>
                                <span class="input-group-text bg-white fw-bold text-muted">/ {{ $tongDiemToiDaTuLuan }} điểm</span>
                            </div>
                            <small class="text-muted">GV2 nhập tổng điểm tự luận sau khi hai bên đã trao đổi thống nhất.</small>
                        @else
                            <div class="fs-3 fw-bold text-success">
                                {{ $baithi->diem_chot !== null ? (float)$baithi->diem_chot . ' / ' . $tongDiemToiDaTuLuan . ' điểm' : '— (Chờ GV2 nhập)' }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-7">
                        <label class="form-label fw-bold">Ghi chú &amp; Lý do thống nhất</label>
                        @if($isGV2)
                            <textarea name="ly_do_thong_nhat" class="form-control" rows="2" placeholder="Nhập lý do hoặc nội dung thống nhất điểm...">{{ old('ly_do_thong_nhat', $baithi->ly_do_thong_nhat) }}</textarea>
                        @else
                            <div class="p-2 bg-white rounded border text-muted">
                                {{ $baithi->ly_do_thong_nhat ?: 'Chưa có ghi chú từ GV2.' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BÀI LÀM TỰ LUẬN ĐỂ THAM KHẢO --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="fw-bold mb-0 text-muted"><i class="bi bi-eye me-1"></i>Xem lại bài làm tự luận của sinh viên</h6>
        </div>
        <div class="card-body">
            @foreach ($baithi->cauTraLois as $ctl)
                @if(in_array($ctl->cauHoi->loai_cau, ['tu_luan', 'tuluan']))
                    <div class="mb-3 border-bottom pb-3">
                        <p class="fw-bold text-dark mb-1">Câu hỏi (Tối đa {{ $ctl->cauHoi->diem }} điểm):</p>
                        <p class="text-muted mb-2">{!! nl2br(e($ctl->cauHoi->noi_dung)) !!}</p>
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small fw-bold d-block mb-1">Bài làm:</span>
                            <div style="white-space: pre-wrap;">{{ $ctl->bai_lam_tu_luan ?: '(Không trả lời)' }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- KHU VỰC THAO TÁC --}}
    <div class="card border-0 shadow-sm p-3 mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                @if($isGV2)
                    <span class="text-muted small d-block"><i class="bi bi-info-circle me-1"></i>GV2 nhập tổng điểm đã thống nhất và nhấn "Lưu điểm thống nhất" để gửi cho GV1 xác nhận.</span>
                @endif
                @if($isGV1)
                    <span class="text-muted small d-block"><i class="bi bi-info-circle me-1"></i>GV1 kiểm tra tổng điểm thống nhất từ GV2 và bấm "GV1 Xác nhận kết quả" để chốt bài thi chính thức.</span>
                @endif
            </div>

            <div class="d-flex gap-2">
                @if($isGV2)
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i> Lưu điểm thống nhất
                    </button>
                @endif

                @php
                    $daCoKetQuaGV2 = ($baithi->diem_chot !== null) || !empty($baithi->ly_do_thong_nhat);
                @endphp

                @if($isGV1 && $daCoKetQuaGV2)
                    <button type="submit" name="action" value="xac_nhan" class="btn btn-success px-4" onclick="return confirm('Xác nhận kết quả thống nhất điểm tự luận để chốt bài thi?');">
                        <i class="bi bi-check2-all me-1"></i> GV1 Xác nhận kết quả (Chốt điểm)
                    </button>
                @elseif($isGV1 && !$daCoKetQuaGV2)
                    <span class="text-warning fw-semibold align-self-center"><i class="bi bi-hourglass-split me-1"></i>Chờ GV2 nhập điểm thống nhất trước</span>
                @endif
            </div>
        </div>
    </div>
</form>
@endsection
