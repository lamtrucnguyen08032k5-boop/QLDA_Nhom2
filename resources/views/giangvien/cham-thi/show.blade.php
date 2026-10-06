@extends('layouts.app')
@section('title', 'Chấm bài thi tự luận')
@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('giangvien.cham-thi.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
    <div>
        @if($isTurnGV1)
            <span class="badge bg-primary">Lượt chấm của bạn: Giảng viên 1 (Lượt 1)</span>
        @elseif($isTurnGV2)
            <span class="badge bg-info text-dark">Lượt chấm của bạn: Giảng viên 2 (Lượt 2)</span>
        @else
            <span class="badge bg-secondary">Chế độ xem (Chỉ đọc)</span>
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

<form method="POST" action="{{ route('giangvien.cham-thi.luu', $baithi->id) }}">
    @csrf

    @foreach ($baithi->cauTraLois as $ctl)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Câu {{ $loop->iteration }} — {{ $ctl->cauHoi->loai_cau === 'tracnghiem' ? 'Trắc nghiệm' : 'Tự luận' }}</span>
                    <span class="badge bg-secondary">Tối đa {{ $ctl->cauHoi->diem }} điểm</span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3 border-bottom pb-2">
                    <p class="fw-semibold text-dark mb-1">Nội dung câu hỏi:</p>
                    <p class="mb-0">{!! nl2br(e($ctl->cauHoi->noi_dung)) !!}</p>
                </div>

                @if ($ctl->cauHoi->loai_cau === 'tracnghiem')
                    <div class="bg-light p-3 rounded">
                        <p class="mb-1">Sinh viên chọn: <strong>{{ $ctl->dap_an_chon ?? '(không trả lời)' }}</strong> • Đáp án đúng: <strong>{{ $ctl->cauHoi->dap_an_dung }}</strong></p>
                        <p class="mb-0 text-success fw-bold">Điểm đạt tự động: {{ $ctl->diem_dat }}</p>
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Bài làm tự luận của sinh viên:</label>
                        <div class="border rounded p-3 bg-light" style="min-height: 100px; white-space: pre-wrap;">{{ $ctl->bai_lam_tu_luan ?: '(Sinh viên không trả lời câu hỏi này)' }}</div>
                    </div>

                    {{-- Hiển thị kết quả điểm đã chấm của GV1 (khi GV2 đang chấm hoặc ở chế độ chỉ đọc) --}}
                    <div class="row g-2 mb-3">
                        @if(($isTurnGV2 || $isReadOnly) && $ctl->diem_gv1 !== null)
                            <div class="col-md-6">
                                <div class="alert alert-info py-2 px-3 mb-0">
                                    <small class="fw-bold d-block"><i class="bi bi-info-circle me-1"></i>Điểm GV1 đã chấm:</small>
                                    <span class="fw-bold text-primary fs-6">{{ $ctl->diem_gv1 }} / {{ $ctl->cauHoi->diem }} điểm</span>
                                </div>
                            </div>
                        @endif
                        @if($isReadOnly && $ctl->diem_gv2 !== null)
                            <div class="col-md-6">
                                <div class="alert alert-success py-2 px-3 mb-0">
                                    <small class="fw-bold d-block"><i class="bi bi-check-circle me-1"></i>Điểm GV2 đã chấm:</small>
                                    <span class="fw-bold text-success fs-6">{{ $ctl->diem_gv2 }} / {{ $ctl->cauHoi->diem }} điểm</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(!$isReadOnly)
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">
                                    {{ $isTurnGV1 ? 'Nhập điểm GV1 chấm' : 'Nhập điểm GV2 chấm' }} (tối đa {{ $ctl->cauHoi->diem }})
                                </label>
                                @php
                                    if ($isTurnGV1) {
                                        $valDiem = ($baithi->trang_thai === 'cho_cham_1') ? null : $ctl->diem_gv1;
                                    } else {
                                        $valDiem = ($baithi->trang_thai === 'cho_cham_2') ? null : $ctl->diem_gv2;
                                    }
                                @endphp
                                <input type="number" step="0.1" min="0" max="{{ $ctl->cauHoi->diem }}"
                                       name="diem[{{ $ctl->id }}]"
                                       class="form-control"
                                       value="{{ old('diem.'.$ctl->id, $valDiem) }}">
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endforeach

    {{-- Nhận xét của Giảng viên --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title fw-bold mb-0">Nhận xét của Giảng viên</h5>
        </div>
        <div class="card-body">
            @if($isTurnGV1)
                {{-- GV1 đang chấm: Chỉ hiện ô nhập nhận xét của GV1 --}}
                @php
                    $valNhanXet1 = ($baithi->trang_thai === 'cho_cham_1') ? '' : $baithi->nhan_xet_1;
                @endphp
                <div class="mb-0">
                    <label class="form-label fw-semibold">Nhận xét của Giảng viên 1</label>
                    <textarea name="nhan_xet_1" class="form-control" rows="3" placeholder="Nhập nhận xét bài làm của sinh viên (nếu có)...">{{ old('nhan_xet_1', $valNhanXet1) }}</textarea>
                </div>
            @elseif($isTurnGV2)
                {{-- GV2 đang chấm: Hiện nhận xét của GV1 + ô nhập nhận xét của GV2 --}}
                @php
                    $valNhanXet2 = ($baithi->trang_thai === 'cho_cham_2') ? '' : $baithi->nhan_xet_2;
                @endphp
                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary"><i class="bi bi-chat-left-text me-1"></i>Nhận xét của Giảng viên 1:</label>
                    <div class="p-3 bg-light rounded border text-dark">
                        {{ $baithi->nhan_xet_1 ?: '(Giảng viên 1 không có nhận xét)' }}
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold text-info">Nhận xét của Giảng viên 2</label>
                    <textarea name="nhan_xet_2" class="form-control" rows="3" placeholder="Nhập nhận xét bài làm của sinh viên (nếu có)...">{{ old('nhan_xet_2', $valNhanXet2) }}</textarea>
                </div>
            @else
                {{-- Chế độ chỉ đọc --}}
                @if($baithi->nhan_xet_1 !== null)
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-primary"><i class="bi bi-chat-left-text me-1"></i>Nhận xét của Giảng viên 1:</label>
                        <div class="p-3 bg-light rounded border text-dark">
                            {{ $baithi->nhan_xet_1 ?: '(Không có nhận xét)' }}
                        </div>
                    </div>
                @endif

                @if($baithi->nhan_xet_2 !== null)
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-info"><i class="bi bi-chat-left-text me-1"></i>Nhận xét của Giảng viên 2:</label>
                        <div class="p-3 bg-light rounded border text-dark">
                            {{ $baithi->nhan_xet_2 ?: '(Không có nhận xét)' }}
                        </div>
                    </div>
                @endif

                @if($baithi->nhan_xet_1 === null && $baithi->nhan_xet_2 === null)
                    <p class="text-muted mb-0 fst-italic">Chưa có nhận xét nào từ Giảng viên.</p>
                @endif
            @endif
        </div>
    </div>

    @if(!$isReadOnly)
        <div class="d-flex justify-content-end gap-3 mb-5">
            <button type="submit" name="action" value="nhap" class="btn btn-outline-primary px-4">
                <i class="bi bi-save me-1"></i> Lưu nháp
            </button>
            <button type="submit" name="action" value="gui" class="btn btn-primary px-4" data-confirm="Bạn có chắc chắn muốn gửi kết quả chấm bài này?">
                <i class="bi bi-send me-1"></i> Gửi kết quả chấm
            </button>
        </div>
    @endif
</form>
@endsection
