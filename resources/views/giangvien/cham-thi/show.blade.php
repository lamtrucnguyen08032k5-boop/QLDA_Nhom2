@extends('layouts.app')
@section('title', 'Chấm bài thi tự luận')
@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('giangvien.cham-thi.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
    <div>
        @if($isGV1)
            <span class="badge bg-primary">Vai trò: Giảng viên 1 (Lượt 1)</span>
        @elseif($isGV2)
            <span class="badge bg-info text-dark">Vai trò: Giảng viên 2 (Lượt 2)</span>
        @endif
    </div>
</div>

@if($isGV2ReadOnlyBeforeGV1)
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
        <div>
            <strong>Chế độ chỉ đọc (Chờ GV1):</strong> Giảng viên 1 chưa hoàn tất lượt chấm 1.
            Theo quy định (BR-03 / HĐ16), GV2 chỉ được xem bài làm ở chế độ chỉ đọc và chưa được nhập điểm / gửi kết quả.
        </div>
    </div>
@elseif($isReadOnly)
    <div class="alert alert-secondary d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-lock-fill fs-4 me-3"></i>
        <div>
            <strong>Bài thi đã chốt điểm:</strong> Kết quả chấm thi đã được khóa (HĐ24) và không thể chỉnh sửa.
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

                    {{-- Hiển thị kết quả chấm GV1 cho GV2 xem (BR-04 / HĐ16) --}}
                    @if($isGV2 && $baithi->ngay_cham_1)
                        <div class="alert alert-info py-2 px-3 mb-3">
                            <small class="fw-bold"><i class="bi bi-info-circle me-1"></i>Kết quả GV1 chấm (Chế độ chỉ xem):</small>
                            <div class="fw-bold text-primary">{{ $ctl->diem_gv1 ?? 0 }} / {{ $ctl->cauHoi->diem }} điểm</div>
                        </div>
                    @endif

                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">
                                {{ $isGV1 ? 'Điểm GV1 chấm' : ($isGV2 ? 'Điểm GV2 chấm' : 'Điểm chấm') }} (tối đa {{ $ctl->cauHoi->diem }})
                            </label>
                            @php
                                $valDiem = $isGV1 ? $ctl->diem_gv1 : ($isGV2 ? $ctl->diem_gv2 : $ctl->diem_dat);
                            @endphp
                            <input type="number" step="0.1" min="0" max="{{ $ctl->cauHoi->diem }}"
                                   name="diem[{{ $ctl->id }}]"
                                   class="form-control"
                                   value="{{ old('diem.'.$ctl->id, $valDiem) }}"
                                   {{ $isReadOnly ? 'readonly disabled' : '' }}>
                        </div>
                    </div>
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
            @if($isGV1)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nhận xét của GV1</label>
                    <textarea name="nhan_xet_1" class="form-control" rows="3" {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('nhan_xet_1', $baithi->nhan_xet_1) }}</textarea>
                </div>
            @endif

            @if($isGV2)
                @if($baithi->nhan_xet_1)
                    <div class="mb-3 p-3 bg-light rounded">
                        <label class="form-label fw-semibold text-muted">Nhận xét của GV1 (chỉ đọc):</label>
                        <p class="mb-0 text-dark">{{ $baithi->nhan_xet_1 }}</p>
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nhận xét của GV2</label>
                    <textarea name="nhan_xet_2" class="form-control" rows="3" {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('nhan_xet_2', $baithi->nhan_xet_2) }}</textarea>
                </div>
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
