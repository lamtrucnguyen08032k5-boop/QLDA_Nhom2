@extends('layouts.sinhvien')
@section('title', 'Đăng ký yêu cầu phúc khảo')
@section('content')

<div class="mb-3">
    <a href="{{ route('sinhvien.ketqua.show', $baithi) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại chi tiết kết quả thi
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="bi bi-arrow-counterclockwise text-warning fs-3"></i>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Yêu cầu phúc khảo bài thi</h5>
                    <span class="text-muted small">Thời hạn nhận phúc khảo: trong vòng 7 ngày kể từ ngày công bố kết quả</span>
                </div>
            </div>

            <div class="card-body p-4">
                {{-- Thông báo lỗi validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('sinhvien.phuc-khao.store', $baithi) }}" enctype="multipart/form-data">
                    @csrf

                    {{-- KHỐI 1: THÔNG TIN THÍ SINH & BÀI THI CẦN PHÚC KHẢO --}}
                    <div class="bg-light p-4 rounded-3 mb-4 border">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-person-badge me-2"></i>1. Thông tin thí sinh &amp; Bài thi
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Họ và tên thí sinh</label>
                                <input type="text" class="form-control bg-white fw-bold text-dark" value="{{ $sinhVien->name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Mã sinh viên</label>
                                <input type="text" class="form-control bg-white font-monospace fw-semibold" value="{{ $sinhVien->ma_so }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Kỳ thi / Bài thi</label>
                                <input type="text" class="form-control bg-white fw-semibold text-primary" 
                                       value="{{ $baithi->dangKy?->lichThi?->ten_ky_thi ?? ($baithi->deThi?->ten_de ?? 'Bài thi') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Mã bài thi</label>
                                <input type="text" class="form-control bg-white font-monospace text-primary fw-bold" value="{{ $baithi->ma_bai_thi_hien_thi }}" readonly>
                            </div>
                            <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Điểm trắc nghiệm (máy chấm)</label>
                                <input type="text" class="form-control bg-white fw-semibold text-dark" value="{{ (float)$baithi->diem_tu_dong }} điểm" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Điểm tự luận</label>
                                <input type="text" class="form-control bg-white fw-semibold text-dark" value="{{ (float)($baithi->diem_chot ?? $baithi->diem_cham_tay) }} điểm" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Tổng điểm hiện tại</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white fw-bold {{ $baithi->is_dat ? 'text-success' : 'text-danger' }}" 
                                           value="{{ (float)$baithi->diem_tong }} / 100" readonly>
                                    <span class="input-group-text {{ $baithi->is_dat ? 'bg-success text-white' : 'bg-danger text-white' }} fw-semibold">
                                        {{ $baithi->ket_qua_text }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KHỐI 2: LÝ DO PHÚC KHẢO & MINH CHỨNG --}}
                    <div class="p-4 rounded-3 mb-4 border bg-white">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-pencil-square me-2"></i>2. Lý do phúc khảo &amp; Minh chứng đính kèm
                        </h6>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                Lý do đề nghị phúc khảo <span class="text-danger">*</span>
                            </label>
                            <textarea name="ly_do" class="form-control" rows="4" required minlength="10" maxlength="2000"
                                      placeholder="Trình bày chi tiết lý do phúc khảo (Ví dụ: Em đề nghị chấm lại phần tự luận câu 2 vì đã nêu đủ 3 ý chính nhưng chưa được tính trọn điểm...)">{{ old('ly_do') }}</textarea>
                            <div class="form-text small text-muted">Vui lòng nêu rõ câu hỏi, nội dung cần chấm lại (tối thiểu 10 ký tự).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                Tài liệu / Minh chứng đính kèm (nếu có)
                            </label>
                            <input type="file" name="file_minh_chung" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text small text-muted">Hỗ trợ các định dạng: JPG, PNG, PDF. Kích thước tối đa: 5MB.</div>
                        </div>

                        {{-- Thông tin lệ phí phúc khảo --}}
                        <div class="alert alert-warning d-flex align-items-center justify-content-between p-3 mt-4 mb-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-cash-coin fs-4 text-warning"></i>
                                <div>
                                    <div class="fw-bold text-dark">Lệ phí phúc khảo theo quy định:</div>
                                    <small class="text-muted">Lệ phí được thanh toán trực tuyến qua cổng VNPAY / Thẻ nội địa Napas.</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2 fw-bold">50,000 VNĐ / bài</span>
                            </div>
                        </div>
                    </div>

                    {{-- NÚT BẤM GỬI & TIẾP TỤC --}}
                    <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                        <a href="{{ route('sinhvien.ketqua.show', $baithi) }}" class="btn btn-outline-secondary px-4 fw-semibold">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            Gửi yêu cầu &amp; Đến bước thanh toán <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
