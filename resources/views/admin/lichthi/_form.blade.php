{{-- Datalist gợi ý phòng thi chuẩn --}}
<datalist id="popularRooms">
    <option value="Phòng Máy 101 - Tòa A"></option>
    <option value="Phòng Máy 102 - Tòa A"></option>
    <option value="Phòng Máy 201 - Tòa B"></option>
    <option value="Phòng Máy 202 - Tòa B"></option>
    <option value="Hội trường A1"></option>
    <option value="Hội trường B1"></option>
    <option value="P.301 - Tòa C"></option>
    <option value="P.302 - Tòa C"></option>
    <option value="P.401 - Tòa C"></option>
    <option value="P.402 - Tòa C"></option>
</datalist>

{{-- KHỐI 1: THÔNG TIN KỲ THI --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold text-dark mb-0">1. Thông tin chung về Kỳ thi</h6>
        <span class="text-muted small">Nhập tên đợt thi, năm học, học kỳ và trạng thái mở đăng ký</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">
                    Tên kỳ thi <span class="text-danger">*</span>
                </label>
                <input type="text" name="ten_ky_thi" class="form-control" value="{{ old('ten_ky_thi', $kyThi->ten_ky_thi ?? '') }}" placeholder="Ví dụ: Kỳ thi chuẩn đầu ra CNTT & Tiếng Anh Đợt 1 - 2026" required autofocus>
                <div class="form-text small text-muted">Đặt tên rõ ràng bao gồm đợt thi và năm tổ chức.</div>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-dark">Năm học</label>
                <input type="text" name="nam_hoc" class="form-control" value="{{ old('nam_hoc', $kyThi->nam_hoc ?? date('Y') . '-' . (date('Y') + 1)) }}" placeholder="2025-2026">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-dark">Học kỳ</label>
                <select name="hoc_ky" class="form-select">
                    <option value="Học kỳ 1" {{ old('hoc_ky', $kyThi->hoc_ky ?? '') === 'Học kỳ 1' ? 'selected' : '' }}>Học kỳ 1</option>
                    <option value="Học kỳ 2" {{ old('hoc_ky', $kyThi->hoc_ky ?? '') === 'Học kỳ 2' ? 'selected' : '' }}>Học kỳ 2</option>
                    <option value="Học kỳ hè" {{ old('hoc_ky', $kyThi->hoc_ky ?? '') === 'Học kỳ hè' ? 'selected' : '' }}>Học kỳ hè</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-dark">Trạng thái <span class="text-danger">*</span></label>
                <select name="trang_thai" class="form-select fw-medium" required>
                    <option value="dang_mo_dang_ky" {{ old('trang_thai', $kyThi->trang_thai ?? 'dang_mo_dang_ky') === 'dang_mo_dang_ky' ? 'selected' : '' }}>Đang mở đăng ký</option>
                    <option value="da_dong_dang_ky" {{ old('trang_thai', $kyThi->trang_thai ?? '') === 'da_dong_dang_ky' ? 'selected' : '' }}>Đã đóng đăng ký</option>
                    <option value="dang_dien_ra" {{ old('trang_thai', $kyThi->trang_thai ?? '') === 'dang_dien_ra' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="da_ket_thuc" {{ old('trang_thai', $kyThi->trang_thai ?? '') === 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold text-dark">Mô tả / Ghi chú cho thí sinh</label>
                <textarea name="mo_ta" class="form-control" rows="2" placeholder="Ghi chú thêm về kỳ thi, đối tượng dự thi, yêu cầu mang giấy tờ tùy thân, thời gian có mặt trước giờ thi...">{{ old('mo_ta', $kyThi->mo_ta ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- KHỐI 2: THIẾT LẬP LỊCH THI, CA THI & GÁN PHÒNG THI --}}
<div class="card shadow-sm border-0 rounded-3 mb-5">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h6 class="fw-bold text-dark mb-0">2. Thiết lập Lịch thi, Ca thi &amp; Phòng thi</h6>
            <span class="text-muted small">Phân bổ môn thi, khoa phụ trách chấm thi, các ca thi và phòng thi tương ứng</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-sm" id="btnQuickPresetBoth" title="Tự động thêm cấu hình mẫu gồm cả 2 môn CNTT và Tiếng Anh">
                Tạo nhanh 2 Môn (CNTT &amp; Tiếng Anh)
            </button>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" id="btnAddLichThi">
                + Thêm Lịch thi (Môn/Ngày)
            </button>
        </div>
    </div>
    <div class="card-body p-4 bg-light bg-opacity-25">
        {{-- Cảnh báo xung đột phòng thi theo thời gian thực --}}
        <div id="conflictAlertContainer" class="d-none mb-4">
            <div class="alert alert-danger shadow-sm border-danger mb-0 rounded-3" role="alert">
                <h6 class="alert-heading fw-bold mb-1 text-danger">
                    Phát hiện trùng phòng thi hoặc xung đột thời gian ca thi!
                </h6>
                <div class="small text-dark mb-2">Các phòng thi sau đây đang bị xếp trùng lịch hoạt động trong cùng khoảng thời gian. Vui lòng điều chỉnh lại phòng thi hoặc đổi giờ bắt đầu:</div>
                <ul class="mb-0 ps-3 small text-danger fw-semibold" id="conflictList"></ul>
            </div>
        </div>

        <div id="lichThiContainer">
            @php
                $initialLichThis = [];
                if (old('lich_this')) {
                    $initialLichThis = old('lich_this');
                } elseif (isset($kyThi) && $kyThi->lichThis->isNotEmpty()) {
                    $groups = $kyThi->lichThis->groupBy(function ($item) {
                        return $item->loai_chung_chi . '_' . $item->khoa_id . '_' . $item->ngay_thi->format('Y-m-d');
                    });
                    foreach ($groups as $group) {
                        $first = $group->first();
                        $caThisArr = [];
                        foreach ($group as $ltItem) {
                            $caThisArr[] = [
                                'id' => $ltItem->id,
                                'ma_ca_thi' => $ltItem->ma_ca_thi,
                                'gio_bat_dau' => substr($ltItem->gio_bat_dau, 0, 5),
                                'thoi_gian_thi_phut' => $ltItem->thoi_gian_thi_phut,
                                'phong_thi' => $ltItem->phong_thi,
                                'so_luong_toi_da' => $ltItem->so_luong_toi_da,
                            ];
                        }
                        $initialLichThis[] = [
                            'loai_chung_chi' => $first->loai_chung_chi,
                            'khoa_id' => $first->khoa_id,
                            'ngay_thi' => $first->ngay_thi ? $first->ngay_thi->format('Y-m-d') : '',
                            'han_dang_ky' => $first->han_dang_ky ? $first->han_dang_ky->format('Y-m-d\TH:i') : '',
                            'le_phi' => $first->le_phi,
                            'ca_this' => $caThisArr,
                        ];
                    }
                }

                if (empty($initialLichThis)) {
                    $initialLichThis = [
                        [
                            'loai_chung_chi' => 'cntt',
                            'khoa_id' => $khoas->first()->id ?? 1,
                            'ngay_thi' => date('Y-m-d', strtotime('+7 days')),
                            'han_dang_ky' => date('Y-m-d\T23:59', strtotime('+5 days')),
                            'le_phi' => 200000,
                            'ca_this' => [
                                [
                                    'id' => '',
                                    'ma_ca_thi' => '',
                                    'gio_bat_dau' => '08:00',
                                    'thoi_gian_thi_phut' => 60,
                                    'phong_thi' => 'Phòng Máy 101 - Tòa A',
                                    'so_luong_toi_da' => 40,
                                ],
                            ],
                        ],
                    ];
                }
            @endphp

            @foreach ($initialLichThis as $ltIdx => $ltData)
                @php
                    $isCntt = ($ltData['loai_chung_chi'] ?? 'cntt') === 'cntt';
                @endphp
                <div class="card mb-4 border-0 shadow-sm rounded-3 lich-thi-card" data-lt-idx="{{ $ltIdx }}">
                    {{-- Header của từng môn/lịch thi --}}
                    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $isCntt ? 'bg-primary' : 'bg-purple' }} text-white px-2 py-1 fs-6 lt-badge" style="{{ !$isCntt ? 'background-color: #6f42c1 !important;' : '' }}">
                                <span class="lt-title-text">{{ $isCntt ? 'CNTT' : 'Tiếng Anh' }}</span>
                            </span>
                            <span class="fw-bold text-dark">Lịch thi #<span class="lt-number">{{ $loop->iteration }}</span></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 btn-clone-lt" title="Nhân bản môn thi này">
                                Nhân bản
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-remove-lt" title="Xóa lịch thi này" {{ count($initialLichThis) === 1 ? 'disabled' : '' }}>
                                Xóa
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        {{-- Hàng thông tin cấu hình Môn / Ngày / Hạn ĐK --}}
                        <div class="row g-3 mb-4 p-3 bg-light bg-opacity-75 rounded-3 border">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-dark">Môn / Chứng chỉ <span class="text-danger">*</span></label>
                                <select name="lich_this[{{ $ltIdx }}][loai_chung_chi]" class="form-select form-select-sm lt-subject" required>
                                    <option value="cntt" {{ ($ltData['loai_chung_chi'] ?? '') === 'cntt' ? 'selected' : '' }}>CNTT (Chuẩn đầu ra)</option>
                                    <option value="tienganh" {{ ($ltData['loai_chung_chi'] ?? '') === 'tienganh' ? 'selected' : '' }}>Tiếng Anh (Chuẩn đầu ra)</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-dark">Khoa chấm thi <span class="text-danger">*</span></label>
                                <select name="lich_this[{{ $ltIdx }}][khoa_id]" class="form-select form-select-sm" required>
                                    @foreach ($khoas as $k)
                                        <option value="{{ $k->id }}" {{ (string) ($ltData['khoa_id'] ?? '') === (string) $k->id ? 'selected' : '' }}>{{ $k->ten_khoa }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-dark">Ngày thi <span class="text-danger">*</span></label>
                                <input type="date" name="lich_this[{{ $ltIdx }}][ngay_thi]" class="form-control form-control-sm lt-ngay-thi fw-semibold" value="{{ $ltData['ngay_thi'] ?? '' }}" required>
                            </div>

                            <div class="col-md-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label small fw-semibold text-dark mb-0">Hạn ĐK <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none small btn-auto-han text-primary" style="font-size: 0.72rem;" title="Tự động đặt hạn đăng ký trước ngày thi 5 ngày">
                                        -5 ngày
                                    </button>
                                </div>
                                <input type="datetime-local" name="lich_this[{{ $ltIdx }}][han_dang_ky]" class="form-control form-control-sm lt-han-dk mt-1" value="{{ $ltData['han_dang_ky'] ?? '' }}" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-dark">Lệ phí (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="lich_this[{{ $ltIdx }}][le_phi]" class="form-control form-control-sm lt-le-phi fw-semibold text-success" value="{{ $ltData['le_phi'] ?? 200000 }}" min="0" step="1000" required>
                            </div>
                        </div>

                        {{-- Bảng Ca thi & Phòng thi thuộc Lịch thi này --}}
                        <div class="border rounded-3 p-3 bg-white shadow-xs">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
                                <div>
                                    <span class="fw-bold text-dark small">Danh sách Ca thi &amp; Phòng thi gán cho lịch thi này:</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-success btn-sm py-1 px-2 btn-add-ca" style="font-size: 0.8rem;">
                                        + Thêm ca thi
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0">
                                    <thead class="table-light small text-secondary">
                                        <tr>
                                            <th style="width: 120px;">Mã ca thi</th>
                                            <th style="width: 140px;">Giờ bắt đầu <span class="text-danger">*</span></th>
                                            <th style="width: 170px;">Thời lượng thi <span class="text-danger">*</span></th>
                                            <th style="width: 150px;">Khung giờ (Dự kiến)</th>
                                            <th>Phòng thi <span class="text-danger">*</span></th>
                                            <th style="width: 110px;">Sĩ số tối đa <span class="text-danger">*</span></th>
                                            <th class="text-center" style="width: 110px;">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ca-thi-tbody">
                                        @php
                                            $caThis = $ltData['ca_this'] ?? [[]];
                                        @endphp
                                        @foreach ($caThis as $ctIdx => $ctData)
                                            <tr class="ca-thi-row" data-ca-id="{{ $ctData['id'] ?? '' }}">
                                                <input type="hidden" name="lich_this[{{ $ltIdx }}][ca_this][{{ $ctIdx }}][id]" value="{{ $ctData['id'] ?? '' }}" class="ct-id-input">
                                                <td>
                                                    <input type="text" name="lich_this[{{ $ltIdx }}][ca_this][{{ $ctIdx }}][ma_ca_thi]" class="form-control form-control-sm font-monospace text-center" placeholder="Tự sinh" value="{{ $ctData['ma_ca_thi'] ?? '' }}" title="Để trống nếu muốn hệ thống tự sinh mã">
                                                </td>
                                                <td>
                                                    <input type="time" name="lich_this[{{ $ltIdx }}][ca_this][{{ $ctIdx }}][gio_bat_dau]" class="form-control form-control-sm ct-start fw-semibold" value="{{ $ctData['gio_bat_dau'] ?? '08:00' }}" required>
                                                    <div class="d-flex gap-1 mt-1">
                                                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="07:30" style="font-size: 0.65rem;">07:30</button>
                                                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="09:30" style="font-size: 0.65rem;">09:30</button>
                                                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="13:30" style="font-size: 0.65rem;">13:30</button>
                                                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="15:30" style="font-size: 0.65rem;">15:30</button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" name="lich_this[{{ $ltIdx }}][ca_this][{{ $ctIdx }}][thoi_gian_thi_phut]" class="form-control form-control-sm ct-duration" value="{{ $ctData['thoi_gian_thi_phut'] ?? 60 }}" min="10" max="300" required>
                                                        <span class="input-group-text bg-light small">phút</span>
                                                    </div>
                                                    <div class="d-flex gap-1 mt-1">
                                                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="45" style="font-size: 0.65rem;">45p</button>
                                                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="60" style="font-size: 0.65rem;">60p</button>
                                                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="90" style="font-size: 0.65rem;">90p</button>
                                                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="120" style="font-size: 0.65rem;">120p</button>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-light text-dark border ct-time-badge px-2 py-1">
                                                        <span class="ct-start-display">08:00</span> - <span class="ct-end-display">09:00</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="position-relative">
                                                        <input type="text" name="lich_this[{{ $ltIdx }}][ca_this][{{ $ctIdx }}][phong_thi]" list="popularRooms" class="form-control form-control-sm ct-room" placeholder="Chọn hoặc nhập phòng thi" value="{{ $ctData['phong_thi'] ?? 'Phòng Máy 101 - Tòa A' }}" required>
                                                        <div class="invalid-feedback small font-weight-bold ct-room-feedback"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" name="lich_this[{{ $ltIdx }}][ca_this][{{ $ctIdx }}][so_luong_toi_da]" class="form-control form-control-sm text-center fw-semibold" value="{{ $ctData['so_luong_toi_da'] ?? 40 }}" min="1" required>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-1">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-clone-ca" title="Nhân bản ca thi này">
                                                            Nhân bản
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-ca" title="Xóa ca thi này" {{ count($caThis) === 1 ? 'disabled' : '' }}>
                                                            Xóa
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Sticky Bottom Action Bar with Real-time Summary --}}
<div class="sticky-bottom bg-white border-top shadow-lg py-3 px-4 rounded-top-3 mt-4" style="z-index: 1020;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
            <span class="fw-bold text-dark">Tổng kết:</span>
            <span class="badge bg-light text-dark border px-2 py-1">
                <strong id="sumLichThi">1</strong> Môn thi
            </span>
            <span class="badge bg-light text-dark border px-2 py-1">
                <strong id="sumCaThi">1</strong> Ca thi
            </span>
            <span class="badge bg-light text-dark border px-2 py-1">
                <strong id="sumChoNgoi">40</strong> Chỗ ngồi
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.lichthi.index') }}" class="btn btn-outline-secondary px-3" onclick="return confirmCancel(event);">
                Hủy bỏ
            </a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm" id="btnSubmitForm">
                Lưu kỳ thi
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let nextLtIndex = {{ count($initialLichThis) + 50 }};
    const existingDbSlots = @json($existingSlots ?? []);
    const khoaOptionsHtml = `@foreach ($khoas as $k)<option value="{{ $k->id }}">{{ $k->ten_khoa }}</option>@endforeach`;

    // Chuyển 'HH:MM' sang số phút từ 0h
    function timeToMinutes(timeStr) {
        if (!timeStr) return null;
        const parts = timeStr.split(':');
        if (parts.length < 2) return null;
        return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
    }

    // Chuyển phút sang 'HH:MM'
    function minutesToTime(totalMins) {
        const hours = Math.floor(totalMins / 60) % 24;
        const mins = totalMins % 60;
        return `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
    }

    // Định dạng ngày sang DD/MM/YYYY
    function formatDateDisplay(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        return dateStr;
    }

    // Cập nhật giờ kết thúc cho 1 hàng
    function updateEndTime(row) {
        const startInput = row.querySelector('.ct-start');
        const durationInput = row.querySelector('.ct-duration');
        const startDisplay = row.querySelector('.ct-start-display');
        const endDisplay = row.querySelector('.ct-end-display');

        if (!startInput || !durationInput) return;

        const startMins = timeToMinutes(startInput.value);
        const duration = parseInt(durationInput.value, 10);

        if (startMins === null || isNaN(duration)) {
            if (startDisplay) startDisplay.textContent = '--:--';
            if (endDisplay) endDisplay.textContent = '--:--';
            return;
        }

        const endMins = startMins + duration;
        if (startDisplay) startDisplay.textContent = startInput.value;
        if (endDisplay) endDisplay.textContent = minutesToTime(endMins);
    }

    // Cập nhật badge môn thi khi đổi select
    function updateSubjectBadge(card) {
        const select = card.querySelector('.lt-subject');
        const badge = card.querySelector('.lt-badge');
        const icon = card.querySelector('.lt-icon');
        const titleText = card.querySelector('.lt-title-text');

        if (!select || !badge || !icon || !titleText) return;

        if (select.value === 'cntt') {
            badge.className = 'badge bg-primary text-white px-2 py-1 fs-6 lt-badge';
            badge.style.backgroundColor = '';
            icon.className = 'bi bi-laptop me-1 lt-icon';
            titleText.textContent = 'CNTT';
        } else {
            badge.className = 'badge text-white px-2 py-1 fs-6 lt-badge';
            badge.style.backgroundColor = '#6f42c1';
            icon.className = 'bi bi-translate me-1 lt-icon';
            titleText.textContent = 'Tiếng Anh';
        }
    }

    // Tự động tính hạn đăng ký = Ngày thi - 5 ngày (23:59)
    function autoCalculateHanDangKy(card) {
        const dateInput = card.querySelector('.lt-ngay-thi');
        const hanInput = card.querySelector('.lt-han-dk');
        if (!dateInput || !hanInput || !dateInput.value) return;

        const date = new Date(dateInput.value);
        date.setDate(date.getDate() - 5);
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        hanInput.value = `${y}-${m}-${d}T23:59`;
    }

    // Cập nhật tóm tắt cấu hình ở sticky bar
    function updateSummaryBar() {
        const cards = document.querySelectorAll('.lich-thi-card');
        const sumLichThi = document.getElementById('sumLichThi');
        const sumCaThi = document.getElementById('sumCaThi');
        const sumChoNgoi = document.getElementById('sumChoNgoi');

        let totalCa = 0;
        let totalCho = 0;

        document.querySelectorAll('.ca-thi-row').forEach(r => {
            totalCa++;
            const seatInput = r.querySelector('input[name*="[so_luong_toi_da]"]');
            if (seatInput) {
                totalCho += parseInt(seatInput.value, 10) || 0;
            }
        });

        if (sumLichThi) sumLichThi.textContent = cards.length;
        if (sumCaThi) sumCaThi.textContent = totalCa;
        if (sumChoNgoi) sumChoNgoi.textContent = totalCho;
    }

    // Kiểm tra và hiển thị cảnh báo trùng lặp phòng thi theo thời gian thực
    function checkAllRoomConflicts() {
        const alertBox = document.getElementById('conflictAlertContainer');
        const conflictList = document.getElementById('conflictList');
        if (!alertBox || !conflictList) return false;

        document.querySelectorAll('.ct-room').forEach(input => {
            input.classList.remove('is-invalid');
            const feedback = input.closest('td')?.querySelector('.ct-room-feedback');
            if (feedback) feedback.textContent = '';
        });

        const cards = document.querySelectorAll('.lich-thi-card');
        const activeRows = [];

        cards.forEach((card, cardIdx) => {
            const dateInput = card.querySelector('.lt-ngay-thi');
            const subjectSelect = card.querySelector('.lt-subject');
            const ltNum = card.querySelector('.lt-number')?.textContent.trim() || (cardIdx + 1);
            const ngayThi = dateInput ? dateInput.value.trim() : '';
            const tenMon = subjectSelect ? (subjectSelect.value === 'cntt' ? 'CNTT' : 'Tiếng Anh') : '';

            card.querySelectorAll('.ca-thi-row').forEach((row, rowIdx) => {
                const startInput = row.querySelector('.ct-start');
                const durationInput = row.querySelector('.ct-duration');
                const roomInput = row.querySelector('.ct-room');
                const idInput = row.querySelector('.ct-id-input');

                const startTime = startInput ? startInput.value.trim() : '';
                const duration = durationInput ? parseInt(durationInput.value, 10) : 60;
                const room = roomInput ? roomInput.value.trim() : '';
                const caId = idInput ? idInput.value.trim() : '';

                if (ngayThi && startTime && room && !isNaN(duration)) {
                    const startMins = timeToMinutes(startTime);
                    const endMins = startMins + duration;
                    activeRows.push({
                        rowElement: row,
                        roomInput: roomInput,
                        ltNum: ltNum,
                        rowIdx: rowIdx + 1,
                        caId: caId,
                        tenMon: tenMon,
                        ngayThi: ngayThi,
                        phongThi: room,
                        startMins: startMins,
                        endMins: endMins,
                        startTimeStr: startTime,
                        endTimeStr: minutesToTime(endMins),
                    });
                }
            });
        });

        const conflictMessages = [];
        const conflictingInputMap = new Set();

        // 1. Kiểm tra xung đột nội bộ giữa các ca thi trong form
        for (let i = 0; i < activeRows.length; i++) {
            for (let j = i + 1; j < activeRows.length; j++) {
                const a = activeRows[i];
                const b = activeRows[j];

                if (a.ngayThi === b.ngayThi && a.phongThi.toLowerCase() === b.phongThi.toLowerCase()) {
                    if (a.startMins < b.endMins && a.endMins > b.startMins) {
                        conflictingInputMap.add(a.roomInput);
                        conflictingInputMap.add(b.roomInput);

                        const msg = `Phòng "<strong>${a.phongThi}</strong>" ngày ${formatDateDisplay(a.ngayThi)} bị trùng giữa Lịch thi #${a.ltNum} (${a.startTimeStr} - ${a.endTimeStr}) và Lịch thi #${b.ltNum} (${b.startTimeStr} - ${b.endTimeStr}).`;
                        if (!conflictMessages.includes(msg)) {
                            conflictMessages.push(msg);
                        }
                    }
                }
            }
        }

        // 2. Kiểm tra xung đột với các ca thi đã có sẵn trong cơ sở dữ liệu
        for (let i = 0; i < activeRows.length; i++) {
            const a = activeRows[i];
            for (let dbSlot of existingDbSlots) {
                if (a.caId && String(a.caId) === String(dbSlot.id)) {
                    continue;
                }

                if (a.ngayThi === dbSlot.ngay_thi && a.phongThi.toLowerCase() === (dbSlot.phong_thi || '').toLowerCase()) {
                    const dbStartMins = timeToMinutes(dbSlot.gio_bat_dau);
                    const dbEndMins = dbStartMins + (parseInt(dbSlot.thoi_gian_thi_phut, 10) || 60);

                    if (a.startMins < dbEndMins && a.endMins > dbStartMins) {
                        conflictingInputMap.add(a.roomInput);
                        const msg = `Phòng "<strong>${a.phongThi}</strong>" ngày ${formatDateDisplay(a.ngayThi)} (${a.startTimeStr} - ${a.endTimeStr}) đã được gán cho ca thi "${dbSlot.ma_ca_thi}" (${dbSlot.gio_bat_dau} - ${minutesToTime(dbEndMins)}) thuộc kỳ thi "${dbSlot.ten_ky_thi}".`;
                        if (!conflictMessages.includes(msg)) {
                            conflictMessages.push(msg);
                        }
                    }
                }
            }
        }

        // Đánh dấu viền đỏ
        conflictingInputMap.forEach(input => {
            input.classList.add('is-invalid');
            const feedback = input.closest('td')?.querySelector('.ct-room-feedback');
            if (feedback) {
                feedback.textContent = '⚠️ Trùng phòng với ca thi khác cùng giờ!';
            }
        });

        // Cập nhật alert box
        if (conflictMessages.length > 0) {
            conflictList.innerHTML = conflictMessages.map(m => `<li>${m}</li>`).join('');
            alertBox.classList.remove('d-none');
            return true;
        } else {
            conflictList.innerHTML = '';
            alertBox.classList.add('d-none');
            return false;
        }
    }

    function updateAllEndTimes() {
        document.querySelectorAll('.ca-thi-row').forEach(row => updateEndTime(row));
    }

    function updateAllLtNumbers() {
        const cards = document.querySelectorAll('.lich-thi-card');
        cards.forEach((c, idx) => {
            const num = c.querySelector('.lt-number');
            if (num) num.textContent = idx + 1;
            const btnLt = c.querySelector('.btn-remove-lt');
            if (btnLt) btnLt.disabled = (cards.length === 1);
            updateSubjectBadge(c);
        });
        updateSummaryBar();
    }

    // Khởi tạo ban đầu
    updateAllLtNumbers();
    updateAllEndTimes();
    checkAllRoomConflicts();

    // Lắng nghe thay đổi input
    document.addEventListener('input', function (e) {
        if (e.target.matches('.ct-start') || e.target.matches('.ct-duration')) {
            const row = e.target.closest('.ca-thi-row');
            if (row) updateEndTime(row);
            checkAllRoomConflicts();
            updateSummaryBar();
        } else if (e.target.matches('.ct-room') || e.target.matches('.lt-ngay-thi') || e.target.matches('.lt-subject') || e.target.matches('input[name*="[so_luong_toi_da]"]')) {
            checkAllRoomConflicts();
            updateSummaryBar();
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.matches('.lt-subject')) {
            const card = e.target.closest('.lich-thi-card');
            if (card) updateSubjectBadge(card);
            checkAllRoomConflicts();
        } else if (e.target.matches('.lt-ngay-thi')) {
            const card = e.target.closest('.lich-thi-card');
            if (card) autoCalculateHanDangKy(card);
            checkAllRoomConflicts();
        } else if (e.target.matches('.ct-start') || e.target.matches('.ct-duration') || e.target.matches('.ct-room')) {
            checkAllRoomConflicts();
            updateSummaryBar();
        }
    });

    // Tạo HTML cho 1 Card Lịch thi
    function createLichThiHtml(ltIdx, loaiChungChi, ngayThiStr, hanStr, lePhi, caThis) {
        const isCntt = loaiChungChi === 'cntt';
        let caThisHtml = '';

        caThis.forEach((ct, ctIdx) => {
            caThisHtml += `
            <tr class="ca-thi-row" data-ca-id="">
                <input type="hidden" name="lich_this[${ltIdx}][ca_this][${ctIdx}][id]" value="" class="ct-id-input">
                <td>
                    <input type="text" name="lich_this[${ltIdx}][ca_this][${ctIdx}][ma_ca_thi]" class="form-control form-control-sm font-monospace text-center" placeholder="Tự sinh" value="${ct.ma_ca_thi || ''}">
                </td>
                <td>
                    <input type="time" name="lich_this[${ltIdx}][ca_this][${ctIdx}][gio_bat_dau]" class="form-control form-control-sm ct-start fw-semibold" value="${ct.gio_bat_dau || '08:00'}" required>
                    <div class="d-flex gap-1 mt-1">
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="07:30" style="font-size: 0.65rem;">07:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="09:30" style="font-size: 0.65rem;">09:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="13:30" style="font-size: 0.65rem;">13:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="15:30" style="font-size: 0.65rem;">15:30</button>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="lich_this[${ltIdx}][ca_this][${ctIdx}][thoi_gian_thi_phut]" class="form-control form-control-sm ct-duration" value="${ct.thoi_gian_thi_phut || 60}" min="10" max="300" required>
                        <span class="input-group-text bg-light small">phút</span>
                    </div>
                    <div class="d-flex gap-1 mt-1">
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="45" style="font-size: 0.65rem;">45p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="60" style="font-size: 0.65rem;">60p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="90" style="font-size: 0.65rem;">90p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="120" style="font-size: 0.65rem;">120p</button>
                    </div>
                </td>
                <td class="text-center align-middle">
                    <span class="badge bg-light text-dark border ct-time-badge px-2 py-1">
                        <i class="bi bi-clock me-1 text-primary"></i><span class="ct-start-display">08:00</span> ➔ <span class="ct-end-display">09:00</span>
                    </span>
                </td>
                <td>
                    <div class="position-relative">
                        <input type="text" name="lich_this[${ltIdx}][ca_this][${ctIdx}][phong_thi]" list="popularRooms" class="form-control form-control-sm ct-room" placeholder="Chọn hoặc nhập phòng thi" value="${ct.phong_thi || 'Phòng Máy 101 - Tòa A'}" required>
                        <div class="invalid-feedback small font-weight-bold ct-room-feedback"></div>
                    </div>
                </td>
                <td>
                    <input type="number" name="lich_this[${ltIdx}][ca_this][${ctIdx}][so_luong_toi_da]" class="form-control form-control-sm text-center fw-semibold" value="${ct.so_luong_toi_da || 40}" min="1" required>
                </td>
                <td class="text-center">
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-clone-ca" title="Nhân bản ca thi này">
                            <i class="bi bi-copy"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-ca" title="Xóa ca thi này">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            `;
        });

        return `
        <div class="card mb-4 border-0 shadow-sm rounded-3 lich-thi-card" data-lt-idx="${ltIdx}">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge ${isCntt ? 'bg-primary' : 'bg-purple'} text-white px-2 py-1 fs-6 lt-badge" style="${!isCntt ? 'background-color: #6f42c1 !important;' : ''}">
                        <i class="bi ${isCntt ? 'bi-laptop' : 'bi-translate'} me-1 lt-icon"></i>
                        <span class="lt-title-text">${isCntt ? 'CNTT' : 'Tiếng Anh'}</span>
                    </span>
                    <span class="fw-bold text-dark">Lịch thi #<span class="lt-number">1</span></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 btn-clone-lt" title="Nhân bản môn thi này">
                        <i class="bi bi-copy me-1"></i>Nhân bản môn
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-remove-lt" title="Xóa lịch thi này">
                        <i class="bi bi-trash me-1"></i>Xóa môn
                    </button>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <div class="row g-3 mb-4 p-3 bg-light bg-opacity-75 rounded-3 border">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-dark">Môn / Chứng chỉ <span class="text-danger">*</span></label>
                        <select name="lich_this[${ltIdx}][loai_chung_chi]" class="form-select form-select-sm lt-subject" required>
                            <option value="cntt" ${isCntt ? 'selected' : ''}>💻 CNTT (Chuẩn đầu ra)</option>
                            <option value="tienganh" ${!isCntt ? 'selected' : ''}>🌐 Tiếng Anh (Chuẩn đầu ra)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-dark">Khoa chấm thi <span class="text-danger">*</span></label>
                        <select name="lich_this[${ltIdx}][khoa_id]" class="form-select form-select-sm" required>
                            ${khoaOptionsHtml}
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-dark">Ngày thi <span class="text-danger">*</span></label>
                        <input type="date" name="lich_this[${ltIdx}][ngay_thi]" class="form-control form-control-sm lt-ngay-thi fw-semibold" value="${ngayThiStr}" required>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small fw-semibold text-dark mb-0">Hạn ĐK <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-link p-0 text-decoration-none small btn-auto-han text-primary" style="font-size: 0.72rem;" title="Tự động đặt hạn đăng ký trước ngày thi 5 ngày">
                                ⚡ -5 ngày
                            </button>
                        </div>
                        <input type="datetime-local" name="lich_this[${ltIdx}][han_dang_ky]" class="form-control form-control-sm lt-han-dk mt-1" value="${hanStr}" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-dark">Lệ phí (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="lich_this[${ltIdx}][le_phi]" class="form-control form-control-sm lt-le-phi fw-semibold text-success" value="${lePhi}" min="0" step="1000" required>
                    </div>
                </div>

                <div class="border rounded-3 p-3 bg-white shadow-xs">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
                        <div>
                            <span class="fw-bold text-dark small">
                                <i class="bi bi-clock-history text-primary me-1"></i>Danh sách Ca thi &amp; Phòng thi gán cho lịch thi này:
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm py-1 px-2 btn-add-ca" style="font-size: 0.8rem;">
                                <i class="bi bi-plus-circle me-1"></i>Thêm ca thi
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th style="width: 120px;">Mã ca thi</th>
                                    <th style="width: 140px;">Giờ bắt đầu <span class="text-danger">*</span></th>
                                    <th style="width: 170px;">Thời lượng thi <span class="text-danger">*</span></th>
                                    <th style="width: 150px;">Khung giờ (Dự kiến)</th>
                                    <th>Phòng thi <span class="text-danger">*</span></th>
                                    <th style="width: 110px;">Sĩ số tối đa <span class="text-danger">*</span></th>
                                    <th class="text-center" style="width: 90px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="ca-thi-tbody">
                                ${caThisHtml}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        `;
    }

    // Nút Thêm Lịch thi mới
    document.getElementById('btnAddLichThi').addEventListener('click', function () {
        const container = document.getElementById('lichThiContainer');
        const ltIdx = nextLtIndex++;

        const defaultDate = new Date();
        defaultDate.setDate(defaultDate.getDate() + 7);
        const dateStr = defaultDate.toISOString().split('T')[0];

        const defaultHan = new Date();
        defaultHan.setDate(defaultHan.getDate() + 2);
        const hanStr = defaultHan.toISOString().split('T')[0] + 'T23:59';

        const cardHtml = createLichThiHtml(ltIdx, 'cntt', dateStr, hanStr, 200000, [{
            gio_bat_dau: '08:00',
            thoi_gian_thi_phut: 60,
            phong_thi: 'Phòng Máy 101 - Tòa A',
            so_luong_toi_da: 40,
        }]);

        container.insertAdjacentHTML('beforeend', cardHtml);
        updateAllLtNumbers();
        updateAllEndTimes();
        checkAllRoomConflicts();
    });

    // Tạo nhanh 2 môn CNTT & Tiếng Anh
    document.getElementById('btnQuickPresetBoth').addEventListener('click', function () {
        if (!confirm('Hệ thống sẽ thêm 2 lịch thi mẫu (CNTT & Tiếng Anh). Bạn có muốn tiếp tục?')) {
            return;
        }

        const container = document.getElementById('lichThiContainer');
        const defaultDate1 = new Date();
        defaultDate1.setDate(defaultDate1.getDate() + 7);
        const dateStr1 = defaultDate1.toISOString().split('T')[0];

        const defaultHan1 = new Date();
        defaultHan1.setDate(defaultHan1.getDate() + 2);
        const hanStr1 = defaultHan1.toISOString().split('T')[0] + 'T23:59';

        const defaultDate2 = new Date();
        defaultDate2.setDate(defaultDate2.getDate() + 8);
        const dateStr2 = defaultDate2.toISOString().split('T')[0];

        const defaultHan2 = new Date();
        defaultHan2.setDate(defaultHan2.getDate() + 3);
        const hanStr2 = defaultHan2.toISOString().split('T')[0] + 'T23:59';

        const html1 = createLichThiHtml(nextLtIndex++, 'cntt', dateStr1, hanStr1, 200000, [
            { gio_bat_dau: '08:00', thoi_gian_thi_phut: 60, phong_thi: 'Phòng Máy 101 - Tòa A', so_luong_toi_da: 40 },
            { gio_bat_dau: '09:30', thoi_gian_thi_phut: 60, phong_thi: 'Phòng Máy 101 - Tòa A', so_luong_toi_da: 40 }
        ]);

        const html2 = createLichThiHtml(nextLtIndex++, 'tienganh', dateStr2, hanStr2, 250000, [
            { gio_bat_dau: '08:00', thoi_gian_thi_phut: 90, phong_thi: 'Hội trường A1', so_luong_toi_da: 50 },
            { gio_bat_dau: '10:00', thoi_gian_thi_phut: 90, phong_thi: 'Hội trường A1', so_luong_toi_da: 50 }
        ]);

        container.insertAdjacentHTML('beforeend', html1);
        container.insertAdjacentHTML('beforeend', html2);
        updateAllLtNumbers();
        updateAllEndTimes();
        checkAllRoomConflicts();
    });

    // Click events delegation (Thêm ca, Clone ca, Xóa ca, Clone Lịch thi, Xóa Lịch thi, Preset chip, Auto Hạn)
    document.addEventListener('click', function (e) {
        // Preset set time button
        const setTimeBtn = e.target.closest('.btn-set-time');
        if (setTimeBtn) {
            const row = setTimeBtn.closest('.ca-thi-row');
            const startInput = row.querySelector('.ct-start');
            if (startInput) {
                startInput.value = setTimeBtn.getAttribute('data-time');
                updateEndTime(row);
                checkAllRoomConflicts();
            }
            return;
        }

        // Preset set duration button
        const setDurBtn = e.target.closest('.btn-set-duration');
        if (setDurBtn) {
            const row = setDurBtn.closest('.ca-thi-row');
            const durInput = row.querySelector('.ct-duration');
            if (durInput) {
                durInput.value = setDurBtn.getAttribute('data-dur');
                updateEndTime(row);
                checkAllRoomConflicts();
            }
            return;
        }

        // Auto tính hạn ĐK
        const autoHanBtn = e.target.closest('.btn-auto-han');
        if (autoHanBtn) {
            const card = autoHanBtn.closest('.lich-thi-card');
            if (card) autoCalculateHanDangKy(card);
            return;
        }

        // Thêm Ca thi
        const addCaBtn = e.target.closest('.btn-add-ca');
        if (addCaBtn) {
            const card = addCaBtn.closest('.lich-thi-card');
            const ltIdx = card.getAttribute('data-lt-idx');
            const tbody = card.querySelector('.ca-thi-tbody');
            const ctIdx = tbody.querySelectorAll('.ca-thi-row').length + Math.floor(Math.random() * 1000);

            const rows = tbody.querySelectorAll('.ca-thi-row');
            let nextStart = '09:30';
            let prevRoom = 'Phòng Máy 101 - Tòa A';
            let prevDur = 60;
            let prevSeats = 40;

            if (rows.length > 0) {
                const lastRow = rows[rows.length - 1];
                const lastEnd = lastRow.querySelector('.ct-end-display')?.textContent.trim();
                const lastRoom = lastRow.querySelector('.ct-room')?.value.trim();
                const lastDur = lastRow.querySelector('.ct-duration')?.value;
                const lastSeats = lastRow.querySelector('input[name*="[so_luong_toi_da]"]')?.value;

                if (lastEnd && lastEnd !== '--:--') nextStart = lastEnd;
                if (lastRoom) prevRoom = lastRoom;
                if (lastDur) prevDur = lastDur;
                if (lastSeats) prevSeats = lastSeats;
            }

            const rowHtml = `
            <tr class="ca-thi-row" data-ca-id="">
                <input type="hidden" name="lich_this[${ltIdx}][ca_this][${ctIdx}][id]" value="" class="ct-id-input">
                <td>
                    <input type="text" name="lich_this[${ltIdx}][ca_this][${ctIdx}][ma_ca_thi]" class="form-control form-control-sm font-monospace text-center" placeholder="Tự sinh">
                </td>
                <td>
                    <input type="time" name="lich_this[${ltIdx}][ca_this][${ctIdx}][gio_bat_dau]" class="form-control form-control-sm ct-start fw-semibold" value="${nextStart}" required>
                    <div class="d-flex gap-1 mt-1">
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="07:30" style="font-size: 0.65rem;">07:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="09:30" style="font-size: 0.65rem;">09:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="13:30" style="font-size: 0.65rem;">13:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="15:30" style="font-size: 0.65rem;">15:30</button>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="lich_this[${ltIdx}][ca_this][${ctIdx}][thoi_gian_thi_phut]" class="form-control form-control-sm ct-duration" value="${prevDur}" min="10" max="300" required>
                        <span class="input-group-text bg-light small">phút</span>
                    </div>
                    <div class="d-flex gap-1 mt-1">
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="45" style="font-size: 0.65rem;">45p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="60" style="font-size: 0.65rem;">60p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="90" style="font-size: 0.65rem;">90p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="120" style="font-size: 0.65rem;">120p</button>
                    </div>
                </td>
                <td class="text-center align-middle">
                    <span class="badge bg-light text-dark border ct-time-badge px-2 py-1">
                        <i class="bi bi-clock me-1 text-primary"></i><span class="ct-start-display">--:--</span> ➔ <span class="ct-end-display">--:--</span>
                    </span>
                </td>
                <td>
                    <div class="position-relative">
                        <input type="text" name="lich_this[${ltIdx}][ca_this][${ctIdx}][phong_thi]" list="popularRooms" class="form-control form-control-sm ct-room" placeholder="Chọn hoặc nhập phòng thi" value="${prevRoom}" required>
                        <div class="invalid-feedback small font-weight-bold ct-room-feedback"></div>
                    </div>
                </td>
                <td>
                    <input type="number" name="lich_this[${ltIdx}][ca_this][${ctIdx}][so_luong_toi_da]" class="form-control form-control-sm text-center fw-semibold" value="${prevSeats}" min="1" required>
                </td>
                <td class="text-center">
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-clone-ca" title="Nhân bản ca thi này">
                            <i class="bi bi-copy"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-ca" title="Xóa ca thi này">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            `;

            tbody.insertAdjacentHTML('beforeend', rowHtml);
            updateRemoveButtons(tbody);
            updateEndTime(tbody.lastElementChild);
            checkAllRoomConflicts();
            updateSummaryBar();
            return;
        }

        // Nhân bản (Clone) Ca thi
        const cloneCaBtn = e.target.closest('.btn-clone-ca');
        if (cloneCaBtn) {
            const row = cloneCaBtn.closest('.ca-thi-row');
            const card = cloneCaBtn.closest('.lich-thi-card');
            const ltIdx = card.getAttribute('data-lt-idx');
            const tbody = card.querySelector('.ca-thi-tbody');
            const ctIdx = tbody.querySelectorAll('.ca-thi-row').length + Math.floor(Math.random() * 1000);

            const startVal = row.querySelector('.ct-start')?.value || '08:00';
            const durVal = row.querySelector('.ct-duration')?.value || 60;
            const roomVal = row.querySelector('.ct-room')?.value || 'Phòng Máy 101 - Tòa A';
            const seatsVal = row.querySelector('input[name*="[so_luong_toi_da]"]')?.value || 40;

            // Tính gợi ý giờ ca sau
            const startMins = timeToMinutes(startVal) || 480;
            const nextStartMins = startMins + parseInt(durVal, 10) + 15; // cách 15p
            const nextStartTime = minutesToTime(nextStartMins);

            const cloneHtml = `
            <tr class="ca-thi-row" data-ca-id="">
                <input type="hidden" name="lich_this[${ltIdx}][ca_this][${ctIdx}][id]" value="" class="ct-id-input">
                <td>
                    <input type="text" name="lich_this[${ltIdx}][ca_this][${ctIdx}][ma_ca_thi]" class="form-control form-control-sm font-monospace text-center" placeholder="Tự sinh">
                </td>
                <td>
                    <input type="time" name="lich_this[${ltIdx}][ca_this][${ctIdx}][gio_bat_dau]" class="form-control form-control-sm ct-start fw-semibold" value="${nextStartTime}" required>
                    <div class="d-flex gap-1 mt-1">
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="07:30" style="font-size: 0.65rem;">07:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="09:30" style="font-size: 0.65rem;">09:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="13:30" style="font-size: 0.65rem;">13:30</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-time" data-time="15:30" style="font-size: 0.65rem;">15:30</button>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="lich_this[${ltIdx}][ca_this][${ctIdx}][thoi_gian_thi_phut]" class="form-control form-control-sm ct-duration" value="${durVal}" min="10" max="300" required>
                        <span class="input-group-text bg-light small">phút</span>
                    </div>
                    <div class="d-flex gap-1 mt-1">
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="45" style="font-size: 0.65rem;">45p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="60" style="font-size: 0.65rem;">60p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="90" style="font-size: 0.65rem;">90p</button>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1 btn-set-duration" data-dur="120" style="font-size: 0.65rem;">120p</button>
                    </div>
                </td>
                <td class="text-center align-middle">
                    <span class="badge bg-light text-dark border ct-time-badge px-2 py-1">
                        <i class="bi bi-clock me-1 text-primary"></i><span class="ct-start-display">--:--</span> ➔ <span class="ct-end-display">--:--</span>
                    </span>
                </td>
                <td>
                    <div class="position-relative">
                        <input type="text" name="lich_this[${ltIdx}][ca_this][${ctIdx}][phong_thi]" list="popularRooms" class="form-control form-control-sm ct-room" placeholder="Chọn hoặc nhập phòng thi" value="${roomVal}" required>
                        <div class="invalid-feedback small font-weight-bold ct-room-feedback"></div>
                    </div>
                </td>
                <td>
                    <input type="number" name="lich_this[${ltIdx}][ca_this][${ctIdx}][so_luong_toi_da]" class="form-control form-control-sm text-center fw-semibold" value="${seatsVal}" min="1" required>
                </td>
                <td class="text-center">
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-clone-ca" title="Nhân bản ca thi này">
                            <i class="bi bi-copy"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-ca" title="Xóa ca thi này">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            `;

            row.insertAdjacentHTML('afterend', cloneHtml);
            updateRemoveButtons(tbody);
            updateAllEndTimes();
            checkAllRoomConflicts();
            updateSummaryBar();
            return;
        }

        // Xóa Ca thi
        const removeCaBtn = e.target.closest('.btn-remove-ca');
        if (removeCaBtn) {
            const tbody = removeCaBtn.closest('.ca-thi-tbody');
            if (tbody.querySelectorAll('.ca-thi-row').length > 1) {
                removeCaBtn.closest('.ca-thi-row').remove();
                updateRemoveButtons(tbody);
                checkAllRoomConflicts();
                updateSummaryBar();
            }
            return;
        }

        // Nhân bản (Clone) Lịch thi / Môn thi
        const cloneLtBtn = e.target.closest('.btn-clone-lt');
        if (cloneLtBtn) {
            const card = cloneLtBtn.closest('.lich-thi-card');
            const ltIdx = nextLtIndex++;

            const subj = card.querySelector('.lt-subject')?.value === 'cntt' ? 'tienganh' : 'cntt';
            const dateStr = card.querySelector('.lt-ngay-thi')?.value || '';
            const hanStr = card.querySelector('.lt-han-dk')?.value || '';
            const lePhi = card.querySelector('.lt-le-phi')?.value || 200000;

            const caThis = [];
            card.querySelectorAll('.ca-thi-row').forEach(r => {
                caThis.push({
                    gio_bat_dau: r.querySelector('.ct-start')?.value || '08:00',
                    thoi_gian_thi_phut: r.querySelector('.ct-duration')?.value || 60,
                    phong_thi: r.querySelector('.ct-room')?.value || 'Phòng Máy 101 - Tòa A',
                    so_luong_toi_da: r.querySelector('input[name*="[so_luong_toi_da]"]')?.value || 40,
                });
            });

            const cloneHtml = createLichThiHtml(ltIdx, subj, dateStr, hanStr, lePhi, caThis.length ? caThis : [{
                gio_bat_dau: '08:00', thoi_gian_thi_phut: 60, phong_thi: 'Phòng Máy 101 - Tòa A', so_luong_toi_da: 40
            }]);

            card.insertAdjacentHTML('afterend', cloneHtml);
            updateAllLtNumbers();
            updateAllEndTimes();
            checkAllRoomConflicts();
            return;
        }

        // Xóa Lịch thi
        const removeLtBtn = e.target.closest('.btn-remove-lt');
        if (removeLtBtn) {
            const container = document.getElementById('lichThiContainer');
            if (container.querySelectorAll('.lich-thi-card').length > 1) {
                if (confirm('Bạn có chắc chắn muốn xóa toàn bộ lịch thi của môn này?')) {
                    removeLtBtn.closest('.lich-thi-card').remove();
                    updateAllLtNumbers();
                    checkAllRoomConflicts();
                }
            }
            return;
        }
    });

    // Chặn submit form nếu có xung đột phòng thi
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const hasConflict = checkAllRoomConflicts();
            if (hasConflict) {
                e.preventDefault();
                const alertBox = document.getElementById('conflictAlertContainer');
                if (alertBox) {
                    alertBox.classList.remove('d-none');
                    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                alert('Không thể lưu do có phòng thi bị trùng lịch hoặc giờ thi bị xung đột. Vui lòng kiểm tra các cảnh báo màu đỏ và điều chỉnh lại!');
            }
        });
    });

    function updateRemoveButtons(tbody) {
        const rows = tbody.querySelectorAll('.ca-thi-row');
        rows.forEach(r => {
            const btn = r.querySelector('.btn-remove-ca');
            if (btn) btn.disabled = (rows.length === 1);
        });
    }
});

function confirmCancel(e) {
    if (!confirm('Bạn có chắc chắn muốn hủy thao tác? Các thông tin đang nhập sẽ không được lưu.')) {
        e.preventDefault();
        return false;
    }
    return true;
}
</script>

