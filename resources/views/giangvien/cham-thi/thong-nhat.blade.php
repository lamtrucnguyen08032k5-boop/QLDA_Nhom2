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
        <strong>Phát hiện chênh lệch điểm tự luận:</strong> Kết quả chấm tự luận của Giảng viên 1 và Giảng viên 2 có chênh lệch cần thống nhất.
        Hai Giảng viên tiến hành trao đổi, Giảng viên 2 nhập điểm đã thống nhất và Giảng viên 1 bấm xác nhận để hoàn tất chốt điểm.
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="mb-1"><strong>Sinh viên:</strong> {{ $baithi->dangKy->sinhVien->name ?? 'N/A' }} ({{ $baithi->dangKy->sinhVien->ma_so ?? '' }})</p>
                <p class="mb-0"><strong>Kỳ thi:</strong> {{ $baithi->dangKy->lichThi->ten_ky_thi ?? 'N/A' }}</p>
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title fw-bold mb-0">Bảng so sánh kết quả tự luận & Nhập điểm thống nhất</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Nội dung câu hỏi</th>
                        <th style="width: 120px;" class="text-center">GV1 Chấm</th>
                        <th style="width: 120px;" class="text-center">GV2 Chấm</th>
                        <th style="width: 160px;" class="text-center">Điểm Thống Nhất</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($baithi->cauTraLois as $ctl)
                    @if(in_array($ctl->cauHoi->loai_cau, ['tu_luan', 'tuluan']))
                        <tr>
                            <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{!! nl2br(e($ctl->cauHoi->noi_dung)) !!}</div>
                                <div class="border rounded p-2 bg-light mt-2 small">
                                    <span class="text-muted fw-bold">Bài làm SV:</span> {{ $ctl->bai_lam_tu_luan ?: '(không trả lời)' }}
                                </div>
                            </td>
                            <td class="text-center fw-bold text-primary fs-5">
                                {{ $ctl->diem_gv1 ?? 0 }}
                            </td>
                            <td class="text-center fw-bold text-info fs-5">
                                {{ $ctl->diem_gv2 ?? 0 }}
                            </td>
                            <td class="text-center">
                                @if($isGV2)
                                    {{-- GV2 nhập điểm đã thống nhất --}}
                                    <input type="number" step="0.1" min="0" max="{{ $ctl->cauHoi->diem }}"
                                           name="diem_thong_nhat[{{ $ctl->id }}]"
                                           class="form-control text-center fw-bold border-success"
                                           value="{{ old('diem_thong_nhat.'.$ctl->id, $ctl->diem_dat ?? $ctl->diem_gv2) }}" required>
                                @else
                                    {{-- GV1 hoặc người khác: Chỉ đọc --}}
                                    <span class="fw-bold text-success fs-5">{{ $ctl->diem_dat ?? '—' }}</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light py-2 fw-semibold">Nhận xét GV1 & GV2</div>
                <div class="card-body">
                    <p class="mb-2"><strong>GV1 nhận xét:</strong> {{ $baithi->nhan_xet_1 ?: 'Không có' }}</p>
                    <p class="mb-0"><strong>GV2 nhận xét:</strong> {{ $baithi->nhan_xet_2 ?: 'Không có' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light py-2 fw-semibold">Lý do & Ghi chú thống nhất</div>
                <div class="card-body">
                    @if($isGV2)
                        <textarea name="ly_do_thong_nhat" class="form-control" rows="3" placeholder="Nhập lý do thống nhất điểm (không bắt buộc)...">{{ old('ly_do_thong_nhat', $baithi->ly_do_thong_nhat) }}</textarea>
                    @else
                        <div class="p-3 bg-light rounded min-height-100">
                            {{ $baithi->ly_do_thong_nhat ?: 'Chưa có ghi chú thống nhất từ GV2.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-3 mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                @if($isGV2)
                    <span class="text-muted small d-block"><i class="bi bi-info-circle me-1"></i>GV2 nhập điểm đã thống nhất và nhấn "Lưu điểm thống nhất" để gửi GV1.</span>
                @endif
                @if($isGV1)
                    <span class="text-muted small d-block"><i class="bi bi-info-circle me-1"></i>GV1 kiểm tra kết quả thống nhất từ GV2 và bấm "GV1 Xác nhận kết quả" để chốt điểm chính thức.</span>
                @endif
            </div>

            <div class="d-flex gap-2">
                @if($isGV2)
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i> Lưu điểm thống nhất
                    </button>
                @endif

                @php
                    $daCoKetQuaGV2 = ($hasDiemThongNhat ?? false) || !empty($baithi->ly_do_thong_nhat);
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
