@extends('layouts.app')
@section('title', 'Chấm bài phúc khảo (Giảng viên)')
@section('content')
<div class="mb-3">
    <a href="{{ route('giangvien.phuc-khao.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-7">
                <p class="mb-1"><strong>Sinh viên:</strong> {{ $phuckhao->baiThi->dangKy->sinhVien->name ?? 'N/A' }} ({{ $phuckhao->baiThi->dangKy->sinhVien->ma_so ?? '' }})</p>
                <p class="mb-0"><strong>Kỳ thi / Đề thi:</strong> {{ $phuckhao->baiThi->deThi->ten_de ?? 'N/A' }}</p>
            </div>
            <div class="col-md-5 text-md-end">
                <p class="mb-1"><strong>Điểm hiện tại:</strong> <span class="badge bg-secondary fs-6">{{ $phuckhao->baiThi->diem_tong }}</span></p>
                <p class="mb-0"><strong>Trạng thái:</strong> <span class="badge bg-info text-dark">{{ $phuckhao->trang_thai }}</span></p>
            </div>
        </div>
        <div class="mt-3 p-3 bg-light rounded">
            <strong>Lý do sinh viên đề nghị phúc khảo:</strong>
            <p class="mb-0 text-dark">{!! nl2br(e($phuckhao->ly_do)) !!}</p>
        </div>
    </div>
</div>

<h5 class="fw-bold mb-3">Bài làm của sinh viên & Điểm chấm ban đầu</h5>

@foreach ($phuckhao->baiThi->cauTraLois as $ctl)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light py-2">
            <span class="fw-bold">Câu {{ $loop->iteration }} — {{ $ctl->cauHoi->loai_cau === 'tracnghiem' ? 'Trắc nghiệm' : 'Tự luận' }} ({{ $ctl->cauHoi->diem }} điểm)</span>
        </div>
        <div class="card-body">
            <p class="mb-2">{!! nl2br(e($ctl->cauHoi->noi_dung)) !!}</p>
            @if ($ctl->cauHoi->loai_cau === 'tracnghiem')
                <p class="small text-muted mb-0">Chọn: {{ $ctl->dap_an_chon }} • Đáp án đúng: {{ $ctl->cauHoi->dap_an_dung }} • Điểm đạt: <strong>{{ $ctl->diem_dat }}</strong></p>
            @else
                <div class="border rounded p-3 bg-light mb-2">{!! nl2br(e($ctl->bai_lam_tu_luan ?: '(Không trả lời)')) !!}</div>
                <div class="d-flex gap-3 small text-muted">
                    <span>GV1 chấm: <strong>{{ $ctl->diem_gv1 ?? 0 }}</strong></span>
                    <span>GV2 chấm: <strong>{{ $ctl->diem_gv2 ?? 0 }}</strong></span>
                    <span>Điểm chốt trước đó: <strong>{{ $ctl->diem_dat }}</strong></span>
                </div>
            @endif
        </div>
    </div>
@endforeach

<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="card-title fw-bold mb-0">Gửi kết quả chấm lại phúc khảo (HĐ12-16)</h5>
    </div>
    <div class="card-body">
        @if($phuckhao->trang_thai === 'dang_xu_ly')
            <form method="POST" action="{{ route('giangvien.phuc-khao.xuly', $phuckhao->id) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Điểm đề xuất sau khi chấm phúc khảo <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" min="0" max="100" name="diem_sau" class="form-control" value="{{ old('diem_sau', $phuckhao->baiThi->diem_tong) }}" required>
                    <small class="text-muted">Nhập điểm tổng đề xuất sau phúc khảo (nếu không đổi, giữ nguyên giá trị điểm hiện tại).</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nhận xét & Phản hồi cho sinh viên <span class="text-danger">*</span></label>
                    <textarea name="phan_hoi" class="form-control" rows="4" placeholder="Nhập lý do điều chỉnh hoặc lý do giữ nguyên điểm..." required>{{ old('phan_hoi', $phuckhao->phan_hoi) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary" data-confirm="Xác nhận gửi kết quả chấm phúc khảo cho Admin duyệt?">
                    <i class="bi bi-send me-1"></i> Gửi kết quả cho Admin duyệt (HĐ16)
                </button>
            </form>
        @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle-fill me-1"></i> Yêu cầu phúc khảo này đã được chấm và gửi cho Admin (Trạng thái: <strong>{{ $phuckhao->trang_thai }}</strong>).
            </div>
            @if($phuckhao->phan_hoi)
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>Đề xuất điểm sau phúc khảo:</strong> {{ $phuckhao->diem_sau }}<br>
                    <strong>Nhận xét:</strong> {{ $phuckhao->phan_hoi }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
