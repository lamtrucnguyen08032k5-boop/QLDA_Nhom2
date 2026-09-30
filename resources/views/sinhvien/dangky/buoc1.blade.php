@extends('layouts.sinhvien')

@section('title', 'Đăng ký dự thi')

@section('content')
@php
    $hideErrorSummary = true;
    $u = $u ?? auth()->user();
@endphp

<ul class="hvnh-steps">
    <li class="active">Bước 1: Cập nhật thông tin cá nhân</li>
    <li>Bước 2: Chọn thông tin đăng ký thi</li>
    <li>Bước 3: Xác nhận và thanh toán</li>
    <li>Bước 4: Trạng thái thanh toán</li>
</ul>

<form method="POST" action="{{ route('sinhvien.dangky.buoc1.luu', $lichthi) }}" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header-blue d-flex justify-content-between align-items-center">
                    <span>Thông tin cá nhân</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên thí sinh</label>
                            <input type="text" class="form-control" value="{{ $u->name }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mã sinh viên</label>
                            <input type="text" class="form-control" value="{{ $u->ma_so }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lớp</label>
                            <input type="text" class="form-control" value="{{ $u->lop }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Khoa</label>
                            <input type="text" class="form-control" value="{{ optional($u->khoa)->ten_khoa }}" disabled>
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-md-6">
                            <label class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" name="ngay_sinh" id="input_ngay_sinh" max="{{ \Illuminate\Support\Carbon::now()->subYears(18)->toDateString() }}" class="form-control @error('ngay_sinh') is-invalid @enderror" value="{{ old('ngay_sinh', $draft['ngay_sinh'] ?? '') }}">
                            <div class="invalid-feedback" id="err_ngay_sinh">@error('ngay_sinh'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Giới tính <span class="text-danger">*</span></label>
                            @php $gt = old('gioi_tinh', $draft['gioi_tinh'] ?? ''); @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('gioi_tinh') is-invalid @enderror" type="radio" name="gioi_tinh" value="nam" id="gt_nam" {{ $gt === 'nam' ? 'checked' : '' }}>
                                <label class="form-check-label" for="gt_nam">Nam</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('gioi_tinh') is-invalid @enderror" type="radio" name="gioi_tinh" value="nu" id="gt_nu" {{ $gt === 'nu' ? 'checked' : '' }}>
                                <label class="form-check-label" for="gt_nu">Nữ</label>
                            </div>
                            <div class="small text-danger mt-1" id="err_gioi_tinh">@error('gioi_tinh'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dân tộc <span class="text-danger">*</span></label>
                            <input type="text" name="dan_toc" id="input_dan_toc" class="form-control @error('dan_toc') is-invalid @enderror" value="{{ old('dan_toc', $draft['dan_toc'] ?? 'Kinh') }}">
                            <div class="invalid-feedback" id="err_dan_toc">@error('dan_toc'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nơi sinh <span class="text-danger">*</span></label>
                            <input type="text" name="noi_sinh" id="input_noi_sinh" class="form-control @error('noi_sinh') is-invalid @enderror" value="{{ old('noi_sinh', $draft['noi_sinh'] ?? '') }}">
                            <div class="invalid-feedback" id="err_noi_sinh">@error('noi_sinh'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CCCD/Số CCCD <span class="text-danger">*</span></label>
                            <input type="text" name="so_cccd" id="input_so_cccd" maxlength="12" placeholder="Nhập số CCCD" class="form-control @error('so_cccd') is-invalid @enderror" value="{{ old('so_cccd', $draft['so_cccd'] ?? '') }}">
                            <div class="invalid-feedback" id="err_so_cccd">@error('so_cccd'){{ $message }}@enderror</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header-blue d-flex justify-content-between align-items-center">
                    <span>Thông tin liên hệ</span>
                </div>
                <div class="card-body">
                    <p class="small text-danger mb-3">Thí sinh vui lòng điền theo địa chỉ sau khi thay đổi địa giới hành chính.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                            <select id="select_tinh" name="tinh_thanh_pho" class="form-select @error('tinh_thanh_pho') is-invalid @enderror" data-old-value="{{ old('tinh_thanh_pho', $draft['tinh_thanh_pho_code'] ?? '') }}">
                                <option value="">Chọn tỉnh/thành phố</option>
                            </select>
                            <div class="invalid-feedback" id="err_tinh_thanh_pho">@error('tinh_thanh_pho'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Xã/Phường <span class="text-danger">*</span></label>
                            <select id="select_xa" name="xa_phuong" class="form-select @error('xa_phuong') is-invalid @enderror" data-old-value="{{ old('xa_phuong', $draft['xa_phuong_code'] ?? '') }}" disabled>
                                <option value="">Chọn xã/phường</option>
                            </select>
                            <div class="invalid-feedback" id="err_xa_phuong">@error('xa_phuong'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số nhà, đường/phố <span class="text-danger">*</span></label>
                            <input type="text" name="dia_chi_chi_tiet" id="input_dia_chi_chi_tiet" class="form-control @error('dia_chi_chi_tiet') is-invalid @enderror" value="{{ old('dia_chi_chi_tiet', $draft['dia_chi_chi_tiet'] ?? '') }}">
                            <div class="invalid-feedback" id="err_dia_chi_chi_tiet">@error('dia_chi_chi_tiet'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" id="input_so_dien_thoai" maxlength="10" placeholder="Nhập số điện thoại" class="form-control @error('so_dien_thoai') is-invalid @enderror" value="{{ old('so_dien_thoai', $draft['so_dien_thoai'] ?? '') }}">
                            <div class="invalid-feedback" id="err_so_dien_thoai">@error('so_dien_thoai'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Email liên hệ <span class="text-danger">*</span></label>
                            <input type="email" name="email_lien_he" id="input_email_lien_he" class="form-control @error('email_lien_he') is-invalid @enderror" value="{{ old('email_lien_he', $draft['email_lien_he'] ?? '') }}" placeholder="Email khác để nhận thông báo (có thể khác email trường)">
                            <div class="invalid-feedback" id="err_email_lien_he">@error('email_lien_he'){{ $message }}@enderror</div>
                            <div class="form-text">Email này dùng để liên hệ khi cần, có thể khác với email tài khoản ({{ $u->email }}).</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header-blue">Thông tin ca thi</div>
                <div class="card-body">
                    <div class="row g-2 small">
                        <div class="col-md-6"><strong>Bài thi:</strong> {{ $lichthi->ten_ky_thi }}</div>
                        <div class="col-md-3"><strong>Ngày thi:</strong> {{ $lichthi->ngay_thi->format('d/m/Y') }}</div>
                        <div class="col-md-3"><strong>Giờ thi:</strong> {{ $lichthi->gio_bat_dau ? \Carbon\Carbon::parse($lichthi->gio_bat_dau)->format('H:i') : '—' }}</div>
                        <div class="col-md-6"><strong>Địa điểm/phòng thi:</strong> {{ $lichthi->phong_thi }}</div>
                        <div class="col-md-6"><strong>Lệ phí:</strong> {{ number_format($lichthi->le_phi) }}đ</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header-blue">Ảnh hồ sơ dự thi</div>
                <div class="card-body">

                    {{-- ẢNH THẺ (chân dung 3x4) --}}
                    <label class="form-label fw-bold mb-1">Ảnh thẻ (Kích thước 3x4cm) <span class="text-danger">*</span></label>
                    <p class="small text-muted mb-1">Vui lòng tuân thủ các yêu cầu tiêu chuẩn về ảnh.</p>
                    <p class="small text-danger mb-2">Lưu ý*: Ảnh đeo kính được coi là không đáp ứng tiêu chuẩn. Nếu ảnh không đáp ứng tiêu chuẩn, thí sinh có thể bị từ chối tham dự kỳ thi.</p>

                    <div class="hvnh-anh-preview mb-1 {{ empty($draft['anh_ho_so']) ? '' : 'has-img' }}" id="preview_box_anh_ho_so">
                        <img id="preview_anh_ho_so" src="{{ !empty($draft['anh_ho_so']) ? \Illuminate\Support\Facades\Storage::url($draft['anh_ho_so']) : '' }}" class="{{ empty($draft['anh_ho_so']) ? 'd-none' : '' }}" alt="Xem trước ảnh thẻ">
                        <span class="placeholder-text {{ empty($draft['anh_ho_so']) ? '' : 'd-none' }}">Chưa có ảnh — bấm chọn tệp bên dưới</span>
                    </div>
                    <input type="file" name="anh_ho_so" id="input_anh_ho_so" data-preview="preview_anh_ho_so" accept="image/*" class="form-control mb-3 @error('anh_ho_so') is-invalid @enderror">
                    <div class="invalid-feedback d-block mt-n2 mb-3" id="err_anh_ho_so">@error('anh_ho_so'){{ $message }}@enderror</div>

                    <hr>

                    {{-- CCCD --}}
                    <label class="form-label fw-bold mb-1">Giấy tờ tuỳ thân (CCCD/Hộ chiếu) <span class="text-danger">*</span></label>
                    <p class="small text-muted mb-2">Giấy tờ cần còn hạn và nguyên gốc, không bong/rách/nứt/gãy, các chi tiết rõ nét, không có dấu hiệu chỉnh sửa.</p>

                    <label class="form-label small text-muted mb-1">Mặt trước</label>
                    <div class="hvnh-anh-preview mb-1 {{ empty($draft['anh_cccd_truoc']) ? '' : 'has-img' }}" id="preview_box_anh_cccd_truoc">
                        <img id="preview_anh_cccd_truoc" src="{{ !empty($draft['anh_cccd_truoc']) ? \Illuminate\Support\Facades\Storage::url($draft['anh_cccd_truoc']) : '' }}" class="{{ empty($draft['anh_cccd_truoc']) ? 'd-none' : '' }}" alt="Xem trước CCCD mặt trước">
                        <span class="placeholder-text {{ empty($draft['anh_cccd_truoc']) ? '' : 'd-none' }}">Chưa có ảnh</span>
                    </div>
                    <input type="file" name="anh_cccd_truoc" id="input_anh_cccd_truoc" data-preview="preview_anh_cccd_truoc" accept="image/*" class="form-control mb-1 @error('anh_cccd_truoc') is-invalid @enderror">
                    <div class="invalid-feedback d-block mb-3" id="err_anh_cccd_truoc">@error('anh_cccd_truoc'){{ $message }}@enderror</div>

                    <label class="form-label small text-muted mb-1">Mặt sau</label>
                    <div class="hvnh-anh-preview mb-1 {{ empty($draft['anh_cccd_sau']) ? '' : 'has-img' }}" id="preview_box_anh_cccd_sau">
                        <img id="preview_anh_cccd_sau" src="{{ !empty($draft['anh_cccd_sau']) ? \Illuminate\Support\Facades\Storage::url($draft['anh_cccd_sau']) : '' }}" class="{{ empty($draft['anh_cccd_sau']) ? 'd-none' : '' }}" alt="Xem trước CCCD mặt sau">
                        <span class="placeholder-text {{ empty($draft['anh_cccd_sau']) ? '' : 'd-none' }}">Chưa có ảnh</span>
                    </div>
                    <input type="file" name="anh_cccd_sau" id="input_anh_cccd_sau" data-preview="preview_anh_cccd_sau" accept="image/*" class="form-control mb-1 @error('anh_cccd_sau') is-invalid @enderror">
                    <div class="invalid-feedback d-block mb-3" id="err_anh_cccd_sau">@error('anh_cccd_sau'){{ $message }}@enderror</div>

                    <hr>

                    {{-- Thẻ SV (khong bat buoc) --}}
                    <label class="form-label mb-1">Ảnh thẻ sinh viên <span class="text-muted small">(không bắt buộc)</span></label>
                    <div class="hvnh-anh-preview mb-1 {{ empty($draft['anh_the_sv']) ? '' : 'has-img' }}" id="preview_box_anh_the_sv">
                        <img id="preview_anh_the_sv" src="{{ !empty($draft['anh_the_sv']) ? \Illuminate\Support\Facades\Storage::url($draft['anh_the_sv']) : '' }}" class="{{ empty($draft['anh_the_sv']) ? 'd-none' : '' }}" alt="Xem trước thẻ sinh viên">
                        <span class="placeholder-text {{ empty($draft['anh_the_sv']) ? '' : 'd-none' }}">Chưa có ảnh</span>
                    </div>
                    <input type="file" name="anh_the_sv" id="input_anh_the_sv" data-preview="preview_anh_the_sv" accept="image/*" class="form-control @error('anh_the_sv') is-invalid @enderror">
                    <div class="invalid-feedback" id="err_anh_the_sv">@error('anh_the_sv'){{ $message }}@enderror</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3 mb-5">
        <a href="{{ route('sinhvien.dangky.index') }}" class="btn btn-outline-secondary">Huỷ</a>
        <button class="btn btn-primary px-4">Tiếp tục</button>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
    input.addEventListener('change', function (e) {
        var file = e.target.files && e.target.files[0];
        var img = document.getElementById(input.dataset.preview);
        var box = document.getElementById('preview_box_' + input.dataset.preview.replace('preview_', ''));
        if (!file || !img) return;
        var url = URL.createObjectURL(file);
        img.src = url;
        img.classList.remove('d-none');
        if (box) {
            box.classList.add('has-img');
            var placeholder = box.querySelector('.placeholder-text');
            if (placeholder) placeholder.classList.add('d-none');
        }
    });
});

// Real-time validation script
(function () {
    var inputCccd = document.getElementById('input_so_cccd');
    var inputPhone = document.getElementById('input_so_dien_thoai');
    var inputEmail = document.getElementById('input_email_lien_he');
    var inputNgaySinh = document.getElementById('input_ngay_sinh');
    var form = document.querySelector('form');

    function setError(inputEl, msgEl, message) {
        if (!inputEl) return;
        inputEl.classList.add('is-invalid');
        if (msgEl) {
            msgEl.textContent = message;
            msgEl.style.display = 'block';
        }
    }

    function clearError(inputEl, msgEl) {
        if (!inputEl) return;
        inputEl.classList.remove('is-invalid');
        if (msgEl) {
            msgEl.textContent = '';
            msgEl.style.display = '';
        }
    }

    function validateCccd(isSubmit) {
        if (!inputCccd) return true;
        var val = inputCccd.value.trim();
        var errEl = document.getElementById('err_so_cccd');
        if (val.length === 0) {
            if (isSubmit) {
                setError(inputCccd, errEl, 'Vui lòng nhập CCCD/Số CCCD.');
                return false;
            } else {
                clearError(inputCccd, errEl);
                return true;
            }
        }
        if (val.length < 12) {
            setError(inputCccd, errEl, 'CCCD/Số CCCD phải bao gồm đúng 12 chữ số.');
            return false;
        }
        clearError(inputCccd, errEl);
        return true;
    }

    function validatePhone(isSubmit) {
        if (!inputPhone) return true;
        var val = inputPhone.value.trim();
        var errEl = document.getElementById('err_so_dien_thoai');
        if (val.length === 0) {
            if (isSubmit) {
                setError(inputPhone, errEl, 'Vui lòng nhập số điện thoại.');
                return false;
            } else {
                clearError(inputPhone, errEl);
                return true;
            }
        }
        if (val.length < 10) {
            setError(inputPhone, errEl, 'Số điện thoại phải bao gồm đúng 10 chữ số.');
            return false;
        }
        clearError(inputPhone, errEl);
        return true;
    }

    function validateEmail(isSubmit) {
        if (!inputEmail) return true;
        var val = inputEmail.value.trim();
        var errEl = document.getElementById('err_email_lien_he');
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (val.length === 0) {
            if (isSubmit) {
                setError(inputEmail, errEl, 'Vui lòng nhập email liên hệ.');
                return false;
            } else {
                clearError(inputEmail, errEl);
                return true;
            }
        }
        if (!emailRegex.test(val)) {
            setError(inputEmail, errEl, 'Email liên hệ không đúng định dạng.');
            return false;
        }
        clearError(inputEmail, errEl);
        return true;
    }

    function validateNgaySinh(isSubmit) {
        if (!inputNgaySinh) return true;
        var val = inputNgaySinh.value;
        var errEl = document.getElementById('err_ngay_sinh');
        if (!val) {
            if (isSubmit) {
                setError(inputNgaySinh, errEl, 'Vui lòng chọn ngày sinh.');
                return false;
            } else {
                clearError(inputNgaySinh, errEl);
                return true;
            }
        }
        var birthDate = new Date(val);
        var today = new Date();
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age < 18) {
            setError(inputNgaySinh, errEl, 'Thí sinh phải từ đủ 18 tuổi trở lên mới được đăng ký dự thi.');
            return false;
        }
        clearError(inputNgaySinh, errEl);
        return true;
    }

    // CCCD input handler (chặn gõ quá 12 số, tự động xoá ký tự không phải số)
    if (inputCccd) {
        inputCccd.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 12);
            validateCccd(false);
        });
        inputCccd.addEventListener('blur', function () {
            validateCccd(false);
        });
    }

    // Phone input handler (chặn gõ quá 10 số, tự động xoá ký tự không phải số)
    if (inputPhone) {
        inputPhone.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
            validatePhone(false);
        });
        inputPhone.addEventListener('blur', function () {
            validatePhone(false);
        });
    }

    // Email input handler
    if (inputEmail) {
        inputEmail.addEventListener('input', function () {
            validateEmail(false);
        });
        inputEmail.addEventListener('blur', function () {
            validateEmail(false);
        });
    }

    // Ngày sinh input handler
    if (inputNgaySinh) {
        inputNgaySinh.addEventListener('change', function () {
            validateNgaySinh(false);
        });
        inputNgaySinh.addEventListener('blur', function () {
            validateNgaySinh(false);
        });
    }

    // Tự động xoá lỗi khi nhập lại các trường text/select/file
    ['input_dan_toc', 'input_noi_sinh', 'input_dia_chi_chi_tiet'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                if (this.value.trim()) {
                    clearError(this, document.getElementById('err_' + id.replace('input_', '')));
                }
            });
        }
    });

    var radiosGt = form ? form.querySelectorAll('input[name="gioi_tinh"]') : [];
    radiosGt.forEach(function(r) {
        r.addEventListener('change', function() {
            var errGt = document.getElementById('err_gioi_tinh');
            if (errGt) { errGt.textContent = ''; errGt.style.display = ''; }
            radiosGt.forEach(function(item) { item.classList.remove('is-invalid'); });
        });
    });

    ['input_anh_ho_so', 'input_anh_cccd_truoc', 'input_anh_cccd_sau'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function() {
                if (this.files.length) {
                    clearError(this, document.getElementById('err_' + id.replace('input_', '')));
                }
            });
        }
    });

    // Form submit validation (chỉ báo lỗi thiếu khi bấm nút Tiếp tục)
    if (form) {
        form.addEventListener('submit', function (e) {
            var isValid = true;

            var v1 = validateNgaySinh(true);
            var v2 = validateCccd(true);
            var v3 = validatePhone(true);
            var v4 = validateEmail(true);

            // Tỉnh / Xã
            var selectTinh = document.getElementById('select_tinh');
            var selectXa = document.getElementById('select_xa');
            var errTinh = document.getElementById('err_tinh_thanh_pho');
            var errXa = document.getElementById('err_xa_phuong');

            if (selectTinh && !selectTinh.value) {
                setError(selectTinh, errTinh, 'Vui lòng chọn tỉnh/thành phố.');
                isValid = false;
            }
            if (selectXa && (!selectXa.value || selectXa.disabled)) {
                setError(selectXa, errXa, 'Vui lòng chọn xã/phường.');
                isValid = false;
            }

            // Text fields bắt buộc khác
            var requiredText = [
                { id: 'input_dan_toc', err: 'err_dan_toc', msg: 'Vui lòng nhập dân tộc.' },
                { id: 'input_noi_sinh', err: 'err_noi_sinh', msg: 'Vui lòng nhập nơi sinh.' },
                { id: 'input_dia_chi_chi_tiet', err: 'err_dia_chi_chi_tiet', msg: 'Vui lòng nhập số nhà, đường/phố.' }
            ];

            requiredText.forEach(function (f) {
                var el = document.getElementById(f.id);
                var errEl = document.getElementById(f.err);
                if (el && !el.value.trim()) {
                    setError(el, errEl, f.msg);
                    isValid = false;
                }
            });

            // Giới tính
            var gtSelected = form.querySelector('input[name="gioi_tinh"]:checked');
            var errGt = document.getElementById('err_gioi_tinh');
            if (!gtSelected) {
                if (errGt) {
                    errGt.textContent = 'Vui lòng chọn giới tính.';
                    errGt.style.display = 'block';
                }
                radiosGt.forEach(function(item) { item.classList.add('is-invalid'); });
                isValid = false;
            }

            // Ảnh bắt buộc (Ảnh hồ sơ, CCCD trước, CCCD sau)
            var hasDraftHoSo = {{ !empty($draft['anh_ho_so']) ? 'true' : 'false' }};
            var hasDraftCccdTruoc = {{ !empty($draft['anh_cccd_truoc']) ? 'true' : 'false' }};
            var hasDraftCccdSau = {{ !empty($draft['anh_cccd_sau']) ? 'true' : 'false' }};

            var inputHoSo = document.getElementById('input_anh_ho_so');
            var inputCccdTruoc = document.getElementById('input_anh_cccd_truoc');
            var inputCccdSau = document.getElementById('input_anh_cccd_sau');

            var errHoSo = document.getElementById('err_anh_ho_so');
            var errCccdTruoc = document.getElementById('err_anh_cccd_truoc');
            var errCccdSau = document.getElementById('err_anh_cccd_sau');

            if (!hasDraftHoSo && inputHoSo && !inputHoSo.files.length) {
                setError(inputHoSo, errHoSo, 'Vui lòng tải lên ảnh hồ sơ dự thi.');
                isValid = false;
            }
            if (!hasDraftCccdTruoc && inputCccdTruoc && !inputCccdTruoc.files.length) {
                setError(inputCccdTruoc, errCccdTruoc, 'Vui lòng tải lên ảnh CCCD mặt trước.');
                isValid = false;
            }
            if (!hasDraftCccdSau && inputCccdSau && !inputCccdSau.files.length) {
                setError(inputCccdSau, errCccdSau, 'Vui lòng tải lên ảnh CCCD mặt sau.');
                isValid = false;
            }

            if (!v1 || !v2 || !v3 || !v4 || !isValid) {
                e.preventDefault();
                var firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
        });
    }
})();

// Cascading dropdown Tỉnh/Thành phố -> Xã/Phường, dữ liệu tĩnh 34 tỉnh/thành sau sáp nhập 2025
(function () {
    var selectTinh = document.getElementById('select_tinh');
    var selectXa = document.getElementById('select_xa');
    if (!selectTinh || !selectXa) return;

    var oldTinh = selectTinh.dataset.oldValue || '';
    var oldXa = selectXa.dataset.oldValue || '';

    fetch('{{ asset("data/vn-address.json") }}')
        .then(function (res) { return res.json(); })
        .then(function (data) {
            data.forEach(function (tinh) {
                var opt = document.createElement('option');
                opt.value = tinh.c;
                opt.textContent = tinh.n;
                if (tinh.c === oldTinh) opt.selected = true;
                selectTinh.appendChild(opt);
            });

            function napXaPhuong(maTinh, maXaChon) {
                selectXa.innerHTML = '<option value="">Chọn xã/phường</option>';
                var tinh = data.find(function (t) { return t.c === maTinh; });
                if (!tinh) { selectXa.disabled = true; return; }
                tinh.w.forEach(function (xa) {
                    var opt = document.createElement('option');
                    opt.value = xa.c;
                    opt.textContent = xa.n;
                    if (xa.c === maXaChon) opt.selected = true;
                    selectXa.appendChild(opt);
                });
                selectXa.disabled = false;
            }

            if (oldTinh) napXaPhuong(oldTinh, oldXa);

            selectTinh.addEventListener('change', function () {
                napXaPhuong(this.value, null);
                if (this.value) {
                    selectTinh.classList.remove('is-invalid');
                    var errTinh = document.getElementById('err_tinh_thanh_pho');
                    if (errTinh) errTinh.textContent = '';
                }
            });

            selectXa.addEventListener('change', function () {
                if (this.value) {
                    selectXa.classList.remove('is-invalid');
                    var errXa = document.getElementById('err_xa_phuong');
                    if (errXa) errXa.textContent = '';
                }
            });
        })
        .catch(function () {
            selectTinh.innerHTML = '<option value="">Không tải được danh sách tỉnh/thành</option>';
        });
})();
</script>
@endsection
