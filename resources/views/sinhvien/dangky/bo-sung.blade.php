@extends('layouts.sinhvien')

@section('title', 'Bổ sung hồ sơ đăng ký thi')

@section('content')
@php
    $u = $u ?? auth()->user();
    $truongSua = is_array($dangky->truong_can_bo_sung) ? $dangky->truong_can_bo_sung : [];
    $canEdit = function($field) use ($truongSua, $isExpired) {
        return ! $isExpired && in_array($field, $truongSua);
    };
@endphp

<div class="mb-3">
    <a href="{{ route('sinhvien.dangky.cua-toi') }}" class="text-decoration-none text-secondary">
        &larr; Quay lại Danh sách đăng ký của tôi
    </a>
</div>

@if ($isExpired)
    <!-- LUỒNG PHỤ: HẾT HẠN BỔ SUNG TRỰC TUYẾN -->
    <div class="alert alert-danger shadow-sm border-danger p-4 mb-4">
        <div class="d-flex align-items-center mb-2">
            <span class="fs-2 me-2">🛑</span>
            <h5 class="alert-heading fw-bold text-danger mb-0">Đã hết hạn bổ sung hồ sơ trực tuyến</h5>
        </div>
        <p class="mb-3 fs-6">
            Đã hết hạn bổ sung hồ sơ trực tuyến. Vui lòng bổ sung hồ sơ trực tiếp tại <strong>Phòng Khảo thí – Phòng 101, Tòa nhà D1, Học viện Ngân hàng, 12 Chùa Bộc, Đống Đa, Hà Nội</strong>, chậm nhất trước ngày <strong>{{ $dangky->hanCuoiBoSungTrucTiep() }}</strong>. Nếu không hoàn tất bổ sung hồ sơ đúng thời hạn, hồ sơ đăng ký dự thi của bạn sẽ bị hủy và lệ phí thi đã thanh toán sẽ không được hoàn lại.
        </p>
        <div class="bg-white p-3 rounded border border-danger-subtle">
            <h6 class="fw-bold text-dark mb-2">📍 Thông tin tiếp nhận bổ sung trực tiếp:</h6>
            <ul class="mb-0 small text-dark ps-3">
                <li class="mb-1"><strong>Địa chỉ Phòng Khảo thí:</strong> Phòng 101, Tòa nhà D1, Học viện Ngân hàng (Số 12 Chùa Bộc, Đống Đa, Hà Nội)</li>
                <li class="mb-1"><strong>Số điện thoại / Email:</strong> 024.3852.1852 &bull; Email: phongkhaothi@hvnh.edu.vn</li>
                <li class="mb-1"><strong>Hạn cuối bổ sung trực tiếp:</strong> <span class="badge bg-danger fs-6">{{ $dangky->hanCuoiBoSungTrucTiep() }}</span></li>
                <li class="mb-0"><strong>Trạng thái hồ sơ:</strong> <span class="badge bg-warning text-dark">{{ $dangky->nhanTrangThaiLabel() }} (Quá hạn trực tuyến)</span></li>
            </ul>
        </div>
    </div>
@else
    @php
        $mapTenTruong = [
            'ngay_sinh'        => 'Ngày sinh',
            'gioi_tinh'        => 'Giới tính',
            'dan_toc'          => 'Dân tộc',
            'noi_sinh'         => 'Nơi sinh',
            'so_cccd'          => 'Số CCCD/Định danh',
            'so_dien_thoai'    => 'Số điện thoại',
            'tinh_thanh_pho'   => 'Tỉnh/Thành phố',
            'xa_phuong'        => 'Xã/Phường',
            'dia_chi_chi_tiet' => 'Địa chỉ chi tiết',
            'email_lien_he'    => 'Email liên hệ',
            'anh_ho_so'        => 'Ảnh thẻ 3x4',
            'anh_cccd_truoc'   => 'Ảnh CCCD mặt trước',
            'anh_cccd_sau'     => 'Ảnh CCCD mặt sau',
            'anh_the_sv'       => 'Ảnh thẻ sinh viên',
        ];
    @endphp
    <!-- Thông báo yêu cầu bổ sung từ Admin -->
    <div class="alert alert-warning shadow-sm border-warning p-3 mb-4">
        <h6 class="fw-bold text-dark mb-2">⚠️ Yêu cầu bổ sung thông tin từ Admin / Phòng Khảo thí</h6>
        <p class="mb-2" style="white-space: pre-line;"><strong>Lý do / Ghi chú:</strong> {{ $dangky->ly_do_bo_sung }}</p>
        <p class="mb-2"><strong>Các trường cần sửa:</strong> 
            @if(is_array($dangky->truong_can_bo_sung))
                @foreach($dangky->truong_can_bo_sung as $tKey)
                    <span class="badge bg-primary me-1 fw-medium px-2 py-1">{{ $mapTenTruong[$tKey] ?? $tKey }}</span>
                @endforeach
            @endif
        </p>
        <p class="mb-0"><strong>Hạn bổ sung trực tuyến:</strong> <span class="badge bg-danger ms-1">{{ optional($dangky->han_bo_sung)->format('d/m/Y H:i') }}</span></p>
    </div>
@endif

<form method="POST" action="{{ route('sinhvien.dangky.bo-sung.luu', $dangky) }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <!-- Cột trái (col-lg-8): Thông tin cá nhân & Ca thi -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header-blue d-flex justify-content-between align-items-center">
                    <span>Bổ sung hồ sơ đăng ký</span>
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
                            <label class="form-label">
                                Số điện thoại
                                @if(in_array('so_dien_thoai', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được sửa</span> @endif
                            </label>
                            <input type="text" name="so_dien_thoai" maxlength="10" class="form-control @error('so_dien_thoai') is-invalid @enderror" 
                                   value="{{ old('so_dien_thoai', $dangky->so_dien_thoai) }}" 
                                   {{ $canEdit('so_dien_thoai') ? '' : 'disabled' }}>
                            @error('so_dien_thoai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Ngày sinh
                                @if(in_array('ngay_sinh', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được sửa</span> @endif
                            </label>
                            <input type="date" name="ngay_sinh" max="{{ \Illuminate\Support\Carbon::now()->subYears(18)->toDateString() }}" class="form-control @error('ngay_sinh') is-invalid @enderror" 
                                   value="{{ old('ngay_sinh', optional($dangky->ngay_sinh)->format('Y-m-d')) }}" 
                                   {{ $canEdit('ngay_sinh') ? '' : 'disabled' }}>
                            @error('ngay_sinh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label d-block">
                                Giới tính
                                @if(in_array('gioi_tinh', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được sửa</span> @endif
                            </label>
                            @php $gt = old('gioi_tinh', $dangky->gioi_tinh); @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('gioi_tinh') is-invalid @enderror" type="radio" name="gioi_tinh" value="nam" id="gt_nam" {{ $gt === 'nam' ? 'checked' : '' }} {{ $canEdit('gioi_tinh') ? '' : 'disabled' }}>
                                <label class="form-check-label" for="gt_nam">Nam</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('gioi_tinh') is-invalid @enderror" type="radio" name="gioi_tinh" value="nu" id="gt_nu" {{ $gt === 'nu' ? 'checked' : '' }} {{ $canEdit('gioi_tinh') ? '' : 'disabled' }}>
                                <label class="form-check-label" for="gt_nu">Nữ</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('gioi_tinh') is-invalid @enderror" type="radio" name="gioi_tinh" value="khac" id="gt_khac" {{ $gt === 'khac' ? 'checked' : '' }} {{ $canEdit('gioi_tinh') ? '' : 'disabled' }}>
                                <label class="form-check-label" for="gt_khac">Khác</label>
                            </div>
                            @error('gioi_tinh') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Dân tộc
                                @if(in_array('dan_toc', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được sửa</span> @endif
                            </label>
                            <input type="text" name="dan_toc" class="form-control @error('dan_toc') is-invalid @enderror" 
                                   value="{{ old('dan_toc', $dangky->dan_toc) }}" 
                                   {{ $canEdit('dan_toc') ? '' : 'disabled' }}>
                            @error('dan_toc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Nơi sinh
                                @if(in_array('noi_sinh', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được sửa</span> @endif
                            </label>
                            <input type="text" name="noi_sinh" class="form-control @error('noi_sinh') is-invalid @enderror" 
                                   value="{{ old('noi_sinh', $dangky->noi_sinh) }}" 
                                   {{ $canEdit('noi_sinh') ? '' : 'disabled' }}>
                            @error('noi_sinh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Số CCCD
                                @if(in_array('so_cccd', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được sửa</span> @endif
                            </label>
                            <input type="text" name="so_cccd" maxlength="12" class="form-control @error('so_cccd') is-invalid @enderror" 
                                   value="{{ old('so_cccd', $dangky->so_cccd) }}" 
                                   {{ $canEdit('so_cccd') ? '' : 'disabled' }}>
                            @error('so_cccd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header-blue">Thông tin ca thi</div>
                <div class="card-body">
                    <div class="row g-2 small">
                        <div class="col-md-6"><strong>Bài thi:</strong> {{ $dangky->lichThi->ten_ky_thi }}</div>
                        <div class="col-md-3"><strong>Ngày thi:</strong> {{ optional($dangky->lichThi->ngay_thi)->format('d/m/Y') }}</div>
                        <div class="col-md-3"><strong>Ca thi:</strong> {{ $dangky->lichThi->ma_ca_thi }}</div>
                        <div class="col-md-6"><strong>Địa điểm/phòng thi:</strong> {{ $dangky->lichThi->phong_thi }}</div>
                        <div class="col-md-6"><strong>Lệ phí:</strong> {{ number_format($dangky->so_tien) }}đ</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải (col-lg-4): Ảnh hồ sơ dự thi & giấy tờ tuỳ thân -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header-blue">Ảnh hồ sơ dự thi</div>
                <div class="card-body">

                    {{-- ẢNH THỂ 4x6 --}}
                    <label class="form-label fw-bold mb-1">
                        Ảnh thẻ (Kích thước 3x4 / 4x6cm)
                        @if(in_array('anh_ho_so', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được tải lại</span> @endif
                    </label>
                    <div class="hvnh-anh-preview mb-1 {{ empty($dangky->anh_ho_so) ? '' : 'has-img' }}" id="preview_box_anh_ho_so">
                        <img id="preview_anh_ho_so" src="{{ !empty($dangky->anh_ho_so) ? Storage::url($dangky->anh_ho_so) : '' }}" class="{{ empty($dangky->anh_ho_so) ? 'd-none' : '' }}" alt="Xem trước ảnh thẻ">
                        <span class="placeholder-text {{ empty($dangky->anh_ho_so) ? '' : 'd-none' }}">Chưa có ảnh</span>
                    </div>
                    <input type="file" name="anh_ho_so" id="input_anh_ho_so" data-preview="preview_anh_ho_so" accept="image/*" class="form-control mb-3 @error('anh_ho_so') is-invalid @enderror" {{ $canEdit('anh_ho_so') ? '' : 'disabled' }}>
                    @error('anh_ho_so') <div class="invalid-feedback d-block mt-n2 mb-3">{{ $message }}</div> @enderror

                    <hr>

                    {{-- CCCD MẶT TRƯỚC --}}
                    <label class="form-label fw-bold mb-1">
                        Giấy tờ tuỳ thân (CCCD)
                    </label>
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">
                            Mặt trước
                            @if(in_array('anh_cccd_truoc', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được tải lại</span> @endif
                        </label>
                        <div class="hvnh-anh-preview mb-1 {{ empty($dangky->anh_cccd_truoc) ? '' : 'has-img' }}" id="preview_box_anh_cccd_truoc">
                            <img id="preview_anh_cccd_truoc" src="{{ !empty($dangky->anh_cccd_truoc) ? Storage::url($dangky->anh_cccd_truoc) : '' }}" class="{{ empty($dangky->anh_cccd_truoc) ? 'd-none' : '' }}" alt="Xem trước CCCD mặt trước">
                            <span class="placeholder-text {{ empty($dangky->anh_cccd_truoc) ? '' : 'd-none' }}">Chưa có ảnh</span>
                        </div>
                        <input type="file" name="anh_cccd_truoc" id="input_anh_cccd_truoc" data-preview="preview_anh_cccd_truoc" accept="image/*" class="form-control mb-1 @error('anh_cccd_truoc') is-invalid @enderror" {{ $canEdit('anh_cccd_truoc') ? '' : 'disabled' }}>
                        @error('anh_cccd_truoc') <div class="invalid-feedback d-block mb-3">{{ $message }}</div> @enderror
                    </div>

                    {{-- CCCD MẶT SAU --}}
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">
                            Mặt sau
                            @if(in_array('anh_cccd_sau', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được tải lại</span> @endif
                        </label>
                        <div class="hvnh-anh-preview mb-1 {{ empty($dangky->anh_cccd_sau) ? '' : 'has-img' }}" id="preview_box_anh_cccd_sau">
                            <img id="preview_anh_cccd_sau" src="{{ !empty($dangky->anh_cccd_sau) ? Storage::url($dangky->anh_cccd_sau) : '' }}" class="{{ empty($dangky->anh_cccd_sau) ? 'd-none' : '' }}" alt="Xem trước CCCD mặt sau">
                            <span class="placeholder-text {{ empty($dangky->anh_cccd_sau) ? '' : 'd-none' }}">Chưa có ảnh</span>
                        </div>
                        <input type="file" name="anh_cccd_sau" id="input_anh_cccd_sau" data-preview="preview_anh_cccd_sau" accept="image/*" class="form-control mb-1 @error('anh_cccd_sau') is-invalid @enderror" {{ $canEdit('anh_cccd_sau') ? '' : 'disabled' }}>
                        @error('anh_cccd_sau') <div class="invalid-feedback d-block mb-3">{{ $message }}</div> @enderror
                    </div>

                    <hr>

                    {{-- THẺ SINH VIÊN --}}
                    <label class="form-label mb-1">
                        Ảnh thẻ sinh viên
                        @if(in_array('anh_the_sv', $truongSua)) <span class="badge bg-warning text-dark ms-1">Được tải lại</span> @endif
                    </label>
                    <div class="hvnh-anh-preview mb-1 {{ empty($dangky->anh_the_sv) ? '' : 'has-img' }}" id="preview_box_anh_the_sv">
                        <img id="preview_anh_the_sv" src="{{ !empty($dangky->anh_the_sv) ? Storage::url($dangky->anh_the_sv) : '' }}" class="{{ empty($dangky->anh_the_sv) ? 'd-none' : '' }}" alt="Xem trước thẻ sinh viên">
                        <span class="placeholder-text {{ empty($dangky->anh_the_sv) ? '' : 'd-none' }}">Chưa có ảnh</span>
                    </div>
                    <input type="file" name="anh_the_sv" id="input_anh_the_sv" data-preview="preview_anh_the_sv" accept="image/*" class="form-control @error('anh_the_sv') is-invalid @enderror" {{ $canEdit('anh_the_sv') ? '' : 'disabled' }}>
                    @error('anh_the_sv') <div class="invalid-feedback">{{ $message }}</div> @enderror

                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3 mb-5">
        <a href="{{ route('sinhvien.dangky.cua-toi') }}" class="btn btn-outline-secondary">Hủy</a>
        @if ($isExpired)
            <button type="button" class="btn btn-secondary px-4" disabled>Đã hết hạn bổ sung trực tuyến</button>
        @else
            <button type="submit" class="btn btn-primary px-4">Tiếp tục / Nộp bổ sung hồ sơ</button>
        @endif
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
</script>
@endsection
