@extends('layouts.sinhvien')
@section('title', 'Đăng ký nhận chứng chỉ bản cứng')
@section('content')

<div class="mb-3">
    <a href="{{ route('sinhvien.chung-nhan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách chứng nhận
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="bi bi-card-checklist text-primary fs-4"></i>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Đăng ký nhận chứng chỉ bản cứng</h5>
                    <span class="text-muted small">Vui lòng kiểm tra kỹ thông tin xác nhận trước khi gửi yêu cầu</span>
                </div>
            </div>

            <div class="card-body p-4">
                {{-- Hiển thị lỗi validation nếu có --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('sinhvien.chung-nhan.store', $baithi) }}">
                    @csrf

                    {{-- KHỐI 1: THÔNG TIN XÁC NHẬN --}}
                    <div class="bg-light p-4 rounded-3 mb-4 border">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-person-badge me-2"></i>1. Thông tin xác nhận thí sinh
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Họ và tên sinh viên</label>
                                <input type="text" class="form-control bg-white fw-bold text-dark" value="{{ $sinhVien->name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Mã sinh viên</label>
                                <input type="text" class="form-control bg-white font-monospace fw-semibold" value="{{ $sinhVien->ma_so }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Lớp / Khóa học</label>
                                <input type="text" class="form-control bg-white" value="{{ $sinhVien->lop ?? 'K22CLC1' }} / {{ $sinhVien->khoa_hoc ?? 'K22' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Khoa / Đơn vị</label>
                                <input type="text" class="form-control bg-white" value="{{ $sinhVien->khoa?->ten_khoa ?? 'Khoa Công nghệ thông tin' }}" readonly>
                            </div>
                            <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>
                            <div class="col-md-8">
                                <label class="form-label small text-muted mb-1">Bài thi đạt chuẩn</label>
                                <input type="text" class="form-control bg-white fw-semibold text-primary" value="{{ $baithi->deThi?->ten_de ?? ($baithi->dangKy?->lichThi?->ten_ky_thi ?? 'Bài thi chứng chỉ') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Điểm đạt được</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white fw-bold text-success" value="{{ (float)$baithi->diem_tong }} / 100 điểm" readonly>
                                    <span class="input-group-text bg-success text-white fw-semibold">ĐẠT</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KHỐI 2: HÌNH THỨC NHẬN CHỨNG CHỈ --}}
                    <div class="p-4 rounded-3 mb-4 border bg-white">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-geo-alt me-2"></i>2. Hình thức nhận chứng chỉ
                        </h6>

                        {{-- Dropdown hình thức nhận --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                Chọn hình thức nhận <span class="text-danger">*</span>
                            </label>
                            <select name="hinh_thuc_nhan" id="selectHinhThucNhan" class="form-select form-select-lg" required>
                                <option value="truc_tiep" {{ old('hinh_thuc_nhan') === 'truc_tiep' ? 'selected' : '' }}>
                                    🏫 Nhận trực tiếp tại trường (Văn phòng Trung tâm Khảo thí - P.102 Tòa A1)
                                </option>
                                <option value="buu_dien" {{ old('hinh_thuc_nhan') === 'buu_dien' ? 'selected' : '' }}>
                                    📦 Nhận qua bưu điện (Chuyển phát nhanh đến địa chỉ cá nhân)
                                </option>
                            </select>
                        </div>

                        {{-- Thông báo khi nhận trực tiếp --}}
                        <div id="boxTrucTiep" class="alert alert-info d-flex align-items-start gap-2 mb-3">
                            <i class="bi bi-info-circle-fill fs-5 mt-1 text-primary"></i>
                            <div>
                                <strong class="d-block mb-1">Địa điểm nhận chứng chỉ trực tiếp:</strong>
                                <span>Văn phòng Trung tâm Khảo thí - Phòng 102 Tòa A1, Học viện Ngân hàng (Số 12 Chùa Bộc, Đống Đa, Hà Nội).</span>
                                <div class="small text-muted mt-1">Giờ làm việc: Thứ 2 đến Thứ 6 (Sáng: 08h00 - 11h30; Chiều: 13h30 - 17h00). Vui lòng mang theo Thẻ sinh viên hoặc CCCD khi đến nhận.</div>
                            </div>
                        </div>

                        {{-- Nhập địa chỉ khi nhận qua bưu điện --}}
                        <div id="boxBuuDien" class="mb-3" style="display: none;">
                            <label class="form-label fw-semibold text-dark">
                                Địa chỉ nhận chứng chỉ qua bưu điện <span class="text-danger">*</span>
                            </label>
                            <textarea name="dia_chi_nhan" id="inputDiaChiNhan" class="form-control" rows="3"
                                      placeholder="Ví dụ: Số 123 đường Nguyễn Trãi, Phường Thượng Đình, Quận Thanh Xuân, Hà Nội...">{{ old('dia_chi_nhan', $baithi->dangKy?->dia_chi ?? '') }}</textarea>
                            <div class="form-text small text-muted">Vui lòng ghi chính xác số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố để bưu tá phát tận tay.</div>
                        </div>

                        {{-- Số điện thoại liên hệ --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                Số điện thoại người nhận <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="so_dien_thoai" class="form-control"
                                   value="{{ old('so_dien_thoai', $baithi->dangKy?->so_dien_thoai ?? ($sinhVien->so_dien_thoai ?? '')) }}"
                                   placeholder="Ví dụ: 0912345678" required>
                            <div class="form-text small text-muted">Số điện thoại để nhà trường hoặc bưu tá liên hệ khi gửi/nhận chứng chỉ.</div>
                        </div>

                        {{-- Ghi chú thêm --}}
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-dark">Ghi chú thêm (nếu có)</label>
                            <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Ghi chú thêm về thời gian nhận thuận tiện, người nhận hộ (nếu có)...">{{ old('ghi_chu') }}</textarea>
                        </div>
                    </div>

                    {{-- NÚT BẤM GỬI & HỦY --}}
                    <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                        <a href="{{ route('sinhvien.chung-nhan.index') }}" class="btn btn-outline-secondary px-4 fw-semibold">
                            Hủy
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Gửi đăng ký
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('selectHinhThucNhan');
    const boxTrucTiep = document.getElementById('boxTrucTiep');
    const boxBuuDien = document.getElementById('boxBuuDien');
    const inputDiaChi = document.getElementById('inputDiaChiNhan');

    function toggleHinhThuc() {
        if (select.value === 'buu_dien') {
            boxTrucTiep.style.display = 'none';
            boxBuuDien.style.display = 'block';
            inputDiaChi.setAttribute('required', 'required');
        } else {
            boxTrucTiep.style.display = 'flex';
            boxBuuDien.style.display = 'none';
            inputDiaChi.removeAttribute('required');
        }
    }

    select.addEventListener('change', toggleHinhThuc);
    toggleHinhThuc(); // Run on init
});
</script>
@endsection
