@extends('layouts.app')
@section('title', 'Chấm bài thi tự luận')
@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('giangvien.cham-thi.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
    <div>
        @if($isTurnGV1)
            <span class="badge bg-primary fs-6 px-3 py-2">Lượt chấm của bạn: Giảng viên 1 (Lượt 1)</span>
        @elseif($isTurnGV2)
            <span class="badge bg-info text-dark fs-6 px-3 py-2">Lượt chấm của bạn: Giảng viên 2 (Lượt 2)</span>
        @else
            <span class="badge bg-secondary fs-6 px-3 py-2">Chế độ xem (Chỉ đọc)</span>
        @endif
    </div>
</div>

@if($isNotMyTurn)
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-eye-fill fs-4 me-3"></i>
        <div>
            <strong>Chế độ chỉ đọc (Chưa tới lượt chấm):</strong> Bài thi hiện chưa tới lượt chấm của bạn hoặc đã chuyển sang bước tiếp theo.
            Bạn có thể xem chi tiết bài làm và các điểm số mà Giảng viên đã nhập bên dưới.
        </div>
    </div>
@elseif($isReadOnly)
    <div class="alert alert-secondary d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-lock-fill fs-4 me-3"></i>
        <div>
            <strong>Bài thi đã chốt điểm:</strong> Kết quả chấm thi đã được khóa và không thể chỉnh sửa.
        </div>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="mb-1"><strong>Sinh viên:</strong> {{ $baithi->dangKy->sinhVien->name ?? 'N/A' }} ({{ $baithi->dangKy->sinhVien->ma_so ?? '' }})</p>
                <p class="mb-0"><strong>Kỳ thi:</strong> {{ $baithi->dangKy->lichThi->ten_ky_thi ?? 'N/A' }} • <strong>Đề thi:</strong> {{ $baithi->deThi->ten_de ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Điểm trắc nghiệm (tự động):</strong> <span class="badge bg-success fs-6">{{ $baithi->diem_tu_dong }}</span></p>
                <p class="mb-0"><strong>Trạng thái bài thi:</strong> <span class="badge bg-info text-dark">{{ $baithi->trang_thai }}</span></p>
            </div>
        </div>
    </div>
</div>

{{-- THÔNG BÁO CHO GV2 XEM ĐIỂM GV1 ĐÃ CHẤM --}}
@if(($isTurnGV2 || $isReadOnly) && $baithi->diem_tu_luan_gv1 !== null)
    <div class="card border-primary mb-4 bg-primary-subtle shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-2">
                <i class="bi bi-info-circle-fill me-1"></i>Kết quả chấm của Giảng viên 1 ({{ $baithi->giangVien1->name ?? 'GV1' }}):
            </h6>
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <span class="text-muted small d-block">Điểm tổng tự luận GV1 đã chấm:</span>
                    <span class="fs-4 fw-bold text-primary">{{ (float)$baithi->diem_tu_luan_gv1 }} / {{ $tongDiemToiDaTuLuan }} điểm</span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Thời gian chấm lần 1:</span>
                    <span class="fw-semibold text-dark">{{ optional($baithi->ngay_cham_1)->format('H:i d/m/Y') ?? '—' }}</span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Nhận xét của GV1:</span>
                    <span class="fw-semibold text-dark">{{ $baithi->nhan_xet_1 ?: '(Không có nhận xét)' }}</span>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- KẾT QUẢ CHẤM CỦA GV2 (KHI BÀI ĐÃ CHẤM XONG HOẶC CHẾ ĐỘ XEM) --}}
@if($isReadOnly && $baithi->diem_tu_luan_gv2 !== null)
    <div class="card border-success mb-4 bg-success-subtle shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold text-success mb-2">
                <i class="bi bi-check-circle-fill me-1"></i>Kết quả chấm của Giảng viên 2 ({{ $baithi->giangVien2->name ?? 'GV2' }}):
            </h6>
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <span class="text-muted small d-block">Điểm tổng tự luận GV2 chấm:</span>
                    <span class="fs-4 fw-bold text-success">{{ (float)$baithi->diem_tu_luan_gv2 }} / {{ $tongDiemToiDaTuLuan }} điểm</span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Thời gian chấm lần 2:</span>
                    <span class="fw-semibold text-dark">{{ optional($baithi->ngay_cham_2)->format('H:i d/m/Y') ?? '—' }}</span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Nhận xét của GV2:</span>
                    <span class="fw-semibold text-dark">{{ $baithi->nhan_xet_2 ?: '(Không có nhận xét)' }}</span>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- DANH SÁCH BÀI LÀM CỦA THÍ SINH (CHỈ XEM, KHÔNG NHẬP ĐIỂM TỪNG CÂU) --}}
<div class="mb-4">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-text me-2"></i>Nội dung câu hỏi &amp; Bài làm của sinh viên</h5>

    @foreach ($baithi->cauTraLois as $ctl)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Câu {{ $loop->iteration }} — {{ in_array($ctl->cauHoi->loai_cau, ['tuluan', 'tu_luan']) ? 'Tự luận' : 'Trắc nghiệm' }}</span>
                    <span class="badge bg-secondary">Tối đa {{ $ctl->cauHoi->diem }} điểm</span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3 border-bottom pb-2">
                    <p class="fw-semibold text-dark mb-1">Nội dung câu hỏi:</p>
                    <p class="mb-0">{!! nl2br(e($ctl->cauHoi->noi_dung)) !!}</p>
                </div>

                @if (!in_array($ctl->cauHoi->loai_cau, ['tuluan', 'tu_luan']))
                    <div class="bg-light p-3 rounded">
                        <p class="mb-1">Sinh viên chọn: <strong>{{ $ctl->dap_an_chon ?? '(không trả lời)' }}</strong> • Đáp án đúng: <strong>{{ $ctl->cauHoi->dap_an_dung }}</strong></p>
                        <p class="mb-0 text-success fw-bold">Điểm đạt tự động: {{ $ctl->diem_dat }}</p>
                    </div>
                @else
                    <div class="mb-2">
                        <label class="form-label fw-bold text-primary">Bài làm tự luận của sinh viên:</label>
                        <div class="border rounded p-3 bg-light" style="min-height: 100px; white-space: pre-wrap;">{{ $ctl->bai_lam_tu_luan ?: '(Sinh viên không trả lời câu hỏi này)' }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- KHU VỰC NHẬP TỔNG ĐIỂM PHẦN TỰ LUẬN --}}
@if(!$isReadOnly)
    <form method="POST" action="{{ route('giangvien.cham-thi.luu', $baithi->id) }}">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title fw-bold text-dark mb-0">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Nhập điểm &amp; Nhận xét phần Tự luận
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark fs-6">
                            {{ $isTurnGV1 ? 'Nhập tổng điểm phần tự luận (GV1)' : 'Nhập tổng điểm phần tự luận (GV2)' }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="number" step="0.1" min="0" max="{{ $tongDiemToiDaTuLuan }}"
                                   name="diem_tu_luan"
                                   class="form-control fw-bold fs-4 text-primary"
                                   placeholder="0.0"
                                   value="{{ old('diem_tu_luan', $isTurnGV1 ? $baithi->diem_tu_luan_gv1 : $baithi->diem_tu_luan_gv2) }}"
                                   required>
                            <span class="input-group-text bg-light fw-bold text-muted">/ {{ $tongDiemToiDaTuLuan }} điểm</span>
                        </div>
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle me-1"></i>GV chỉ cần nhập tổng điểm của toàn bộ phần tự luận (từ 0 đến tối đa {{ $tongDiemToiDaTuLuan }} điểm), không cần chấm điểm từng câu lẻ.
                        </small>
                    </div>
                </div>

                @if($isTurnGV1)
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Nhận xét của Giảng viên 1</label>
                        <textarea name="nhan_xet_1" class="form-control" rows="3" placeholder="Nhập nhận xét bài làm của sinh viên (nếu có)...">{{ old('nhan_xet_1', $baithi->nhan_xet_1) }}</textarea>
                    </div>
                @elseif($isTurnGV2)
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-primary">Nhận xét của Giảng viên 2</label>
                        <textarea name="nhan_xet_2" class="form-control" rows="3" placeholder="Nhập nhận xét bài làm của sinh viên (nếu có)...">{{ old('nhan_xet_2', $baithi->nhan_xet_2) }}</textarea>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-5">
            <button type="submit" name="action" value="nhap" class="btn btn-outline-primary px-4">
                <i class="bi bi-save me-1"></i> Lưu nháp
            </button>
            <button type="submit" name="action" value="gui" class="btn btn-primary px-4" data-confirm="Bạn có chắc chắn muốn gửi kết quả chấm bài này?">
                <i class="bi bi-send me-1"></i> Gửi kết quả chấm
            </button>
        </div>
    </form>
@else
    {{-- Chế độ chỉ đọc --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white py-3">
            <h5 class="card-title fw-bold mb-0">Tổng kết điểm tự luận &amp; Nhận xét</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <span class="text-muted small d-block">Điểm tự luận chốt chính thức:</span>
                    <span class="fs-4 fw-bold text-success">{{ (float)($baithi->diem_chot ?? $baithi->diem_cham_tay) }} / {{ $tongDiemToiDaTuLuan }} điểm</span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Tổng điểm bài thi:</span>
                    <span class="fs-4 fw-bold text-primary">{{ (float)$baithi->diem_tong }} / 100 điểm</span>
                </div>
            </div>

            @if($baithi->nhan_xet_1)
                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Nhận xét của Giảng viên 1:</label>
                    <div class="p-3 bg-light rounded border text-dark">{{ $baithi->nhan_xet_1 }}</div>
                </div>
            @endif

            @if($baithi->nhan_xet_2)
                <div class="mb-3">
                    <label class="form-label fw-semibold text-success">Nhận xét của Giảng viên 2:</label>
                    <div class="p-3 bg-light rounded border text-dark">{{ $baithi->nhan_xet_2 }}</div>
                </div>
            @endif

            @if($baithi->ly_do_thong_nhat)
                <div class="mb-0">
                    <label class="form-label fw-semibold text-info">Ghi chú thống nhất điểm:</label>
                    <div class="p-3 bg-light rounded border text-dark">{{ $baithi->ly_do_thong_nhat }}</div>
                </div>
            @endif
        </div>
    </div>
@endif

@endsection
