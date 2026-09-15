@extends('layouts.app')
@section('title', 'Danh sách hồ sơ đăng ký thi')
@section('content')

<div class="mb-3">
    <a href="{{ route('admin.dangky.danhsach') }}" class="text-decoration-none text-secondary">
        &larr; Quay lại Danh sách các lịch thi
    </a>
</div>

<!-- Header thông tin ca thi -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body bg-light rounded">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1 text-primary fw-bold">{{ $lichthi->ten_ky_thi }}</h5>
                <p class="text-muted mb-0 small">
                    <strong>Ngày thi:</strong> {{ optional($lichthi->ngay_thi)->format('d/m/Y') }} &bull;
                    <strong>Ca thi:</strong> <code>{{ $lichthi->ma_ca_thi }}</code> ({{ $lichthi->gio_bat_dau }}) &bull;
                    <strong>Phòng thi:</strong> {{ $lichthi->phong_thi }} &bull;
                    <strong>Chỉ tiêu:</strong> Tối đa {{ $lichthi->so_luong_toi_da }} thí sinh
                </p>
            </div>
            <div>
                <span class="badge bg-primary px-3 py-2 fs-6">
                    Đã duyệt: {{ $lichthi->dangKysDaDuyet()->count() }} / {{ $lichthi->so_luong_toi_da }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Thanh tìm kiếm và bộ lọc sinh viên -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.dangky.index', $lichthi) }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Tìm theo Mã SV, Họ tên, Mã đăng ký, Lớp..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="trang_thai" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái hồ sơ --</option>
                    <option value="cho_thanh_toan" {{ request('trang_thai') === 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="cho_duyet" {{ request('trang_thai') === 'cho_duyet' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="cho_bo_sung" {{ request('trang_thai') === 'cho_bo_sung' ? 'selected' : '' }}>Yêu cầu bổ sung</option>
                    <option value="het_han_bo_sung" {{ request('trang_thai') === 'het_han_bo_sung' ? 'selected' : '' }}>Hết hạn bổ sung</option>
                    <option value="da_bo_sung" {{ request('trang_thai') === 'da_bo_sung' ? 'selected' : '' }}>Đã bổ sung/Chờ duyệt lại</option>
                    <option value="da_duyet" {{ request('trang_thai') === 'da_duyet' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="tu_choi" {{ request('trang_thai') === 'tu_choi' ? 'selected' : '' }}>Từ chối</option>
                    <option value="da_huy" {{ request('trang_thai') === 'da_huy' ? 'selected' : '' }}>Đã huỷ</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Lọc dữ liệu</button>
            </div>
            @if(request()->anyFilled(['q', 'trang_thai']))
                <div class="col-md-2">
                    <a href="{{ route('admin.dangky.index', $lichthi) }}" class="btn btn-sm btn-outline-secondary w-100">Xóa bộ lọc</a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Bảng danh sách sinh viên đăng ký -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Mã đăng ký</th>
                        <th>Họ và tên</th>
                        <th>Mã SV</th>
                        <th>Lớp</th>
                        <th>Khoa</th>
                        <th>Ngày đăng ký</th>
                        <th class="text-center">Trạng thái hồ sơ</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($dangKys as $dk)
                    <tr>
                        <td class="ps-3 fw-bold font-monospace text-dark">{{ $dk->ma_dang_ky }}</td>
                        <td class="fw-semibold">{{ $dk->sinhVien->name }}</td>
                        <td><code>{{ $dk->sinhVien->ma_so }}</code></td>
                        <td>{{ $dk->sinhVien->lop ?? '—' }}</td>
                        <td><small>{{ optional($dk->sinhVien->khoa)->ten_khoa ?? '—' }}</small></td>
                        <td><small class="text-muted">{{ $dk->created_at->format('d/m/Y H:i') }}</small></td>
                        <td class="text-center">
                            @if ($dk->trang_thai_thanh_toan === 'cho_thanh_toan' && $dk->trang_thai !== 'da_huy')
                                <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                            @elseif ($dk->isChoBoSungQuaHan())
                                <span class="badge bg-danger">Hết hạn bổ sung</span>
                            @elseif ($dk->trang_thai === 'cho_duyet')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @elseif ($dk->trang_thai === 'cho_bo_sung')
                                <span class="badge bg-info text-dark">Yêu cầu bổ sung</span>
                            @elseif ($dk->trang_thai === 'da_bo_sung')
                                <span class="badge bg-primary">Đã bổ sung/Chờ duyệt lại</span>
                            @elseif ($dk->trang_thai === 'da_duyet')
                                <span class="badge bg-success">Đã duyệt</span>
                            @elseif ($dk->trang_thai === 'tu_choi')
                                <span class="badge bg-danger">Từ chối</span>
                            @else
                                <span class="badge bg-secondary">Đã huỷ</span>
                            @endif
                        </td>
                        <td class="text-end pe-3 text-nowrap">
                            <!-- Duy nhất nút Xem chi tiết -->
                            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#chiTiet{{ $dk->id }}">
                                🔍 Xem chi tiết
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Không tìm thấy sinh viên đăng ký nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 mb-4">
    {{ $dangKys->links() }}
</div>

<!-- ================= TOÀN BỘ MODAL ĐƯỢC ĐẶT NGOÀI BẢNG ĐỂ TRÁNH LỖI GIAO DIỆN ================= -->
@foreach ($dangKys as $dk)

    <!-- 1. MODAL XEM CHI TIẾT HỒ SƠ & THAO TÁC -->
    <div class="modal fade" id="chiTiet{{ $dk->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Chi tiết hồ sơ đăng ký — {{ $dk->sinhVien->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Status Header -->
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <strong>Trạng thái hiện tại:</strong>
                            <span class="badge {{ $dk->trang_thai_thanh_toan === 'cho_thanh_toan' && $dk->trang_thai !== 'da_huy' ? 'bg-warning text-dark' : 'bg-primary' }} fs-6 ms-2">{{ $dk->nhanTrangThaiLabel() }}</span>
                            @if ($dk->nguoiDuyet)
                                <small class="text-muted ms-3">(Người xử lý gần nhất: {{ $dk->nguoiDuyet->name }})</small>
                            @endif
                        </div>
                        <div>
                            <small class="text-muted">Ngày nộp: {{ $dk->created_at->format('d/m/Y H:i:s') }}</small>
                        </div>
                    </div>

                    @if ($dk->trang_thai_thanh_toan === 'cho_thanh_toan' && $dk->trang_thai !== 'da_huy')
                        <div class="alert alert-warning border-warning d-flex align-items-center mb-4">
                            <div class="fs-3 me-3">⏳</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Hồ sơ đang ở trạng thái: Chờ thanh toán</h6>
                                <p class="mb-0 small text-dark">
                                    Thí sinh chưa hoàn tất nộp lệ phí thi (Hạn chót: <strong>{{ $dk->hanThanhToan()->format('d/m/Y H:i') }}</strong>).
                                    Các chức năng <strong>Duyệt hồ sơ</strong>, <strong>Yêu cầu bổ sung</strong> và <strong>Từ chối</strong> bị vô hiệu hoá và chỉ mở sau khi thí sinh đã thanh toán thành công.
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($dk->trang_thai === 'cho_bo_sung')
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
                        @if ($dk->isHetHanBoSungOnline())
                            <div class="alert alert-danger shadow-sm border-danger">
                                <h6 class="fw-bold mb-1">⏰ Hồ sơ đã HẾT THỜI HẠN bổ sung trực tuyến:</h6>
                                <p class="mb-1">Hồ sơ đã quá thời hạn bổ sung (Thời hạn: <strong>{{ optional($dk->han_bo_sung)->format('d/m/Y H:i') }}</strong>). Sinh viên không thể tự bổ sung online và phải đến trực tiếp Phòng Khảo thí để được cán bộ hỗ trợ.</p>
                                <p class="mb-1" style="white-space: pre-line;"><strong>Nội dung từng yêu cầu bổ sung:</strong> {{ $dk->ly_do_bo_sung }}</p>
                                <p class="mb-0"><strong>Các trường đã yêu cầu:</strong> 
                                    @if(is_array($dk->truong_can_bo_sung))
                                        @foreach($dk->truong_can_bo_sung as $tKey)
                                            <span class="badge bg-danger me-1 fw-medium px-2 py-1">{{ $mapTenTruong[$tKey] ?? $tKey }}</span>
                                        @endforeach
                                    @endif
                                </p>
                            </div>
                        @else
                            <div class="alert alert-warning shadow-sm">
                                <h6 class="fw-bold mb-1">⚠️ Nội dung Admin đã yêu cầu bổ sung:</h6>
                                <p class="mb-1" style="white-space: pre-line;"><strong>Lý do:</strong> {{ $dk->ly_do_bo_sung }}</p>
                                <p class="mb-1"><strong>Các trường cần sửa:</strong> 
                                    @if(is_array($dk->truong_can_bo_sung))
                                        @foreach($dk->truong_can_bo_sung as $tKey)
                                            <span class="badge bg-primary me-1 fw-medium px-2 py-1">{{ $mapTenTruong[$tKey] ?? $tKey }}</span>
                                        @endforeach
                                    @endif
                                </p>
                                <p class="mb-0"><strong>Hạn bổ sung trực tuyến:</strong> {{ optional($dk->han_bo_sung)->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    @endif

                    <div class="row g-4">
                        <!-- Cột trái: Thông tin sinh viên, cá nhân, pháp lý & ca thi -->
                        <div class="col-md-6">
                            <div class="card border mb-3">
                                <div class="card-header bg-light fw-bold">1. Thông tin sinh viên & Tài khoản</div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="text-muted" style="width: 140px;">Mã sinh viên:</td><td class="fw-bold">{{ $dk->sinhVien->ma_so }}</td></tr>
                                        <tr><td class="text-muted">Họ và tên:</td><td class="fw-bold text-primary">{{ $dk->sinhVien->name }}</td></tr>
                                        <tr><td class="text-muted">Lớp:</td><td>{{ $dk->sinhVien->lop ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Khoa:</td><td>{{ optional($dk->sinhVien->khoa)->ten_khoa ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Email tài khoản:</td><td>{{ $dk->sinhVien->email }}</td></tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card border mb-3">
                                <div class="card-header bg-light fw-bold">2. Thông tin cá nhân & Pháp lý</div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="text-muted" style="width: 140px;">Ngày sinh:</td><td>{{ optional($dk->ngay_sinh)->format('d/m/Y') ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Giới tính:</td><td>{{ ucfirst($dk->gioi_tinh ?? '—') }}</td></tr>
                                        <tr><td class="text-muted">Dân tộc:</td><td>{{ $dk->dan_toc ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Nơi sinh:</td><td>{{ $dk->noi_sinh ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Số CCCD:</td><td class="fw-bold">{{ $dk->so_cccd ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Số điện thoại:</td><td>{{ $dk->so_dien_thoai ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Email liên hệ:</td><td>{{ $dk->email_lien_he ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Địa chỉ đầy đủ:</td><td>{{ $dk->diaChiDayDu() ?? '—' }}</td></tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card border">
                                <div class="card-header bg-light fw-bold">3. Thông tin lịch thi & Thanh toán</div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="text-muted" style="width: 140px;">Kỳ thi:</td><td class="fw-bold">{{ $dk->lichThi->ten_ky_thi }}</td></tr>
                                        <tr><td class="text-muted">Thời gian thi:</td><td>{{ optional($dk->lichThi->ngay_thi)->format('d/m/Y') }} ({{ $dk->lichThi->gio_bat_dau }})</td></tr>
                                        <tr><td class="text-muted">Phòng thi:</td><td>{{ $dk->lichThi->phong_thi }}</td></tr>
                                        <tr><td class="text-muted">Lệ phí thi:</td><td><strong class="text-success">{{ number_format($dk->so_tien) }} đ</strong></td></tr>
                                        <tr><td class="text-muted">Thanh toán:</td><td><span class="badge bg-success">{{ $dk->nhanTrangThaiThanhToanLabel() }}</span> (Mã GD: {{ $dk->ma_giao_dich ?? '—' }})</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Cột phải: Hình ảnh tài liệu & Lịch sử xử lý -->
                        <div class="col-md-6">
                            <div class="card border mb-3">
                                <div class="card-header bg-light fw-bold">4. Hình ảnh tài liệu hồ sơ</div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6 text-center">
                                            <small class="d-block text-muted mb-1">Ảnh CCCD Mặt trước</small>
                                            @if ($dk->anh_cccd_truoc)
                                                <a href="{{ Storage::url($dk->anh_cccd_truoc) }}" target="_blank">
                                                    <img src="{{ Storage::url($dk->anh_cccd_truoc) }}" class="img-thumbnail rounded" style="max-height: 120px; object-fit: cover;">
                                                </a>
                                            @else
                                                <span class="text-muted small">Chưa tải lên</span>
                                            @endif
                                        </div>
                                        <div class="col-6 text-center">
                                            <small class="d-block text-muted mb-1">Ảnh CCCD Mặt sau</small>
                                            @if ($dk->anh_cccd_sau)
                                                <a href="{{ Storage::url($dk->anh_cccd_sau) }}" target="_blank">
                                                    <img src="{{ Storage::url($dk->anh_cccd_sau) }}" class="img-thumbnail rounded" style="max-height: 120px; object-fit: cover;">
                                                </a>
                                            @else
                                                <span class="text-muted small">Chưa tải lên</span>
                                            @endif
                                        </div>
                                        <div class="col-6 text-center mt-3">
                                            <small class="d-block text-muted mb-1">Ảnh Hồ sơ 4x6</small>
                                            @if ($dk->anh_ho_so)
                                                <a href="{{ Storage::url($dk->anh_ho_so) }}" target="_blank">
                                                    <img src="{{ Storage::url($dk->anh_ho_so) }}" class="img-thumbnail rounded" style="max-height: 120px; object-fit: cover;">
                                                </a>
                                            @else
                                                <span class="text-muted small">Chưa tải lên</span>
                                            @endif
                                        </div>
                                        <div class="col-6 text-center mt-3">
                                            <small class="d-block text-muted mb-1">Ảnh Thẻ Sinh viên</small>
                                            @if ($dk->anh_the_sv)
                                                <a href="{{ Storage::url($dk->anh_the_sv) }}" target="_blank">
                                                    <img src="{{ Storage::url($dk->anh_the_sv) }}" class="img-thumbnail rounded" style="max-height: 120px; object-fit: cover;">
                                                </a>
                                            @else
                                                <span class="text-muted small">Chưa có</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border">
                                <div class="card-header bg-light fw-bold">5. Lịch sử xử lý hồ sơ (Audit Log)</div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 250px;">
                                        <table class="table table-sm table-striped mb-0 small">
                                            <thead>
                                                <tr>
                                                    <th>Thời gian</th>
                                                    <th>Người xử lý</th>
                                                    <th>Hành động</th>
                                                    <th>Chi tiết / Ghi chú</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($dk->lichSuXuLy as $ls)
                                                    <tr>
                                                        <td class="text-nowrap">{{ $ls->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $ls->user ? $ls->user->name : 'Hệ thống' }} <small class="text-muted">({{ $ls->nhanVaiTroLabel() }})</small></td>
                                                        <td><span class="badge bg-secondary">{{ $ls->nhanHanhDongLabel() }}</span></td>
                                                        <td>{{ $ls->noi_dung }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="4" class="text-center text-muted py-2">Chưa có lịch sử xử lý.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <div class="d-flex gap-2 align-items-center">
                        @if ($dk->trang_thai_thanh_toan !== 'da_thanh_toan')
                            <span class="badge bg-warning text-dark me-1">⏳ Chờ thí sinh thanh toán lệ phí</span>
                            <button class="btn btn-success" disabled title="Chỉ được thao tác sau khi thí sinh hoàn tất thanh toán lệ phí">✅ Duyệt hồ sơ</button>
                            <button class="btn btn-warning text-dark" disabled title="Chỉ được thao tác sau khi thí sinh hoàn tất thanh toán lệ phí">⚠️ Yêu cầu bổ sung</button>
                            <button class="btn btn-danger" disabled title="Chỉ được thao tác sau khi thí sinh hoàn tất thanh toán lệ phí">❌ Từ chối</button>
                        @else
                            @if (in_array($dk->trang_thai, ['cho_duyet', 'da_bo_sung']))
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#duyet{{ $dk->id }}">✅ Duyệt hồ sơ</button>
                                <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#boSung{{ $dk->id }}">⚠️ Yêu cầu bổ sung</button>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#tuChoi{{ $dk->id }}">❌ Từ chối</button>
                            @elseif ($dk->trang_thai === 'cho_bo_sung')
                                @if ($dk->isHetHanBoSungOnline())
                                    <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#hoTroBoSung{{ $dk->id }}">🛠️ Hỗ trợ bổ sung hồ sơ</button>
                                @else
                                    <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#boSung{{ $dk->id }}">✏️ Sửa yêu cầu bổ sung</button>
                                @endif
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#tuChoi{{ $dk->id }}">❌ Từ chối</button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. MODAL XÁC NHẬN DUYỆT -->
    <div class="modal fade" id="duyet{{ $dk->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.dangky.approve', [$lichthi, $dk]) }}" class="modal-content">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Xác nhận duyệt hồ sơ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="fs-1 text-success mb-2">✅</div>
                    <p class="fs-5 mb-1">Bạn có chắc chắn muốn duyệt hồ sơ đăng ký dự thi này không?</p>
                    <p class="text-muted small mb-0">Thí sinh <strong>{{ $dk->sinhVien->name }}</strong> (Mã DK: {{ $dk->ma_dang_ky }}) sẽ được thêm vào danh sách dự thi chính thức.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-success px-4">Xác nhận Duyệt</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. MODAL YÊU CẦU BỔ SUNG -->
    <div class="modal fade" id="boSung{{ $dk->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('admin.dangky.bosung', [$lichthi, $dk]) }}" class="modal-content" novalidate onsubmit="return validateBoSungForm_{{ $dk->id }}(event)">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Yêu cầu bổ sung hồ sơ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="boSungAlert_{{ $dk->id }}" class="alert alert-danger py-2 mb-3 small fw-medium" style="display:none">
                        Vui lòng nhập đầy đủ lý do bổ sung cho tất cả các trường được chọn.
                    </div>

                    <label class="form-label fw-bold text-primary">1. Danh sách các trường được phép chỉnh sửa:</label>
                    <p class="text-muted small">Tích chọn các trường cần sinh viên bổ sung hoặc sửa đổi. Hệ thống chỉ mở khóa duy nhất các trường này.</p>
                    @php
                        $cacTruong = [
                            // --- Thông tin cá nhân ---
                            'ngay_sinh'        => ['nhan' => 'Ngày sinh',              'nhom' => 'Thông tin cá nhân'],
                            'gioi_tinh'        => ['nhan' => 'Giới tính',              'nhom' => 'Thông tin cá nhân'],
                            'dan_toc'          => ['nhan' => 'Dân tộc',                'nhom' => 'Thông tin cá nhân'],
                            'noi_sinh'         => ['nhan' => 'Nơi sinh',               'nhom' => 'Thông tin cá nhân'],
                            'so_cccd'          => ['nhan' => 'Số CCCD/Định danh',      'nhom' => 'Thông tin cá nhân'],
                            // --- Thông tin liên hệ ---
                            'so_dien_thoai'    => ['nhan' => 'Số điện thoại liên hệ', 'nhom' => 'Thông tin liên hệ'],
                            'tinh_thanh_pho'   => ['nhan' => 'Tỉnh/Thành phố',        'nhom' => 'Thông tin liên hệ'],
                            'xa_phuong'        => ['nhan' => 'Xã/Phường',             'nhom' => 'Thông tin liên hệ'],
                            'dia_chi_chi_tiet' => ['nhan' => 'Địa chỉ chi tiết',      'nhom' => 'Thông tin liên hệ'],
                            'email_lien_he'    => ['nhan' => 'Email liên hệ',         'nhom' => 'Thông tin liên hệ'],
                            // --- Hồ sơ ảnh ---
                            'anh_ho_so'        => ['nhan' => 'Ảnh thẻ 3x4',           'nhom' => 'Ảnh hồ sơ dự thi'],
                            'anh_cccd_truoc'   => ['nhan' => 'Ảnh CCCD mặt trước',    'nhom' => 'Ảnh hồ sơ dự thi'],
                            'anh_cccd_sau'     => ['nhan' => 'Ảnh CCCD mặt sau',      'nhom' => 'Ảnh hồ sơ dự thi'],
                            'anh_the_sv'       => ['nhan' => 'Ảnh thẻ sinh viên',     'nhom' => 'Ảnh hồ sơ dự thi'],
                        ];
                        $selFields   = is_array($dk->truong_can_bo_sung) ? $dk->truong_can_bo_sung : [];
                        $savedReason = is_array($dk->ly_do_tung_truong)  ? $dk->ly_do_tung_truong  : [];
                        $nhomList    = array_unique(array_column($cacTruong, 'nhom'));
                    @endphp

                    <div>
                        @foreach($nhomList as $nhom)
                            <p class="fw-semibold text-primary mb-1 mt-3 small text-uppercase" style="letter-spacing:.05em">{{ $nhom }}</p>
                            <div class="bg-light rounded border p-2 mb-2">
                                @foreach($cacTruong as $key => $info)
                                    @if($info['nhom'] === $nhom)
                                        @php $isChecked = in_array($key, $selFields); @endphp
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="truong_can_bo_sung[]"
                                                       value="{{ $key }}"
                                                       id="ts_{{ $dk->id }}_{{ $key }}"
                                                       onchange="
                                                            var box = document.getElementById('ly_do_box_{{ $dk->id }}_{{ $key }}');
                                                            if (this.checked) {
                                                                box.style.display = 'block';
                                                            } else {
                                                                box.style.display = 'none';
                                                                var txt = document.getElementById('ly_do_txt_{{ $dk->id }}_{{ $key }}');
                                                                if(txt) {
                                                                    txt.classList.remove('is-invalid');
                                                                    var err = document.getElementById('err_msg_{{ $dk->id }}_{{ $key }}');
                                                                    if(err) err.style.display = 'none';
                                                                }
                                                            }
                                                       "
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label fw-medium" for="ts_{{ $dk->id }}_{{ $key }}">
                                                    {{ $info['nhan'] }}
                                                </label>
                                            </div>
                                            <div id="ly_do_box_{{ $dk->id }}_{{ $key }}"
                                                 class="mt-1 ps-4"
                                                 style="display: {{ $isChecked ? 'block' : 'none' }}">
                                                <label class="form-label mb-1 text-danger small fw-semibold">Lý do yêu cầu bổ sung <span class="text-danger">*</span></label>
                                                <textarea id="ly_do_txt_{{ $dk->id }}_{{ $key }}"
                                                          name="ly_do_tung_truong[{{ $key }}]"
                                                          class="form-control form-control-sm"
                                                          rows="2"
                                                          placeholder="Lý do bổ sung trường này (bắt buộc)..."
                                                          oninput="this.classList.remove('is-invalid'); var e = document.getElementById('err_msg_{{ $dk->id }}_{{ $key }}'); if(e) e.style.display='none';">{{ $savedReason[$key] ?? '' }}</textarea>
                                                <div id="err_msg_{{ $dk->id }}_{{ $key }}" class="text-danger small mt-1 fw-medium" style="display:none">
                                                    &bull; Vui lòng nhập lý do bổ sung cho trường này.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <script>
                    function validateBoSungForm_{{ $dk->id }}(e) {
                        var form = e.target;
                        var checkboxes = form.querySelectorAll('input[name="truong_can_bo_sung[]"]:checked');
                        var alertBox = document.getElementById('boSungAlert_{{ $dk->id }}');
                        
                        alertBox.style.display = 'none';
                        var isValid = true;
                        var firstErrorInput = null;

                        if (checkboxes.length === 0) {
                            alertBox.innerText = 'Vui lòng chọn ít nhất một trường cần bổ sung.';
                            alertBox.style.display = 'block';
                            e.preventDefault();
                            return false;
                        }

                        checkboxes.forEach(function(cb) {
                            var key = cb.value;
                            var txt = document.getElementById('ly_do_txt_{{ $dk->id }}_' + key);
                            var errMsg = document.getElementById('err_msg_{{ $dk->id }}_' + key);
                            if (txt) {
                                if (!txt.value.trim()) {
                                    isValid = false;
                                    txt.classList.add('is-invalid');
                                    if (errMsg) errMsg.style.display = 'block';
                                    if (!firstErrorInput) firstErrorInput = txt;
                                } else {
                                    txt.classList.remove('is-invalid');
                                    if (errMsg) errMsg.style.display = 'none';
                                }
                            }
                        });

                        if (!isValid) {
                            alertBox.innerText = 'Vui lòng nhập đầy đủ lý do bổ sung cho các trường được tích chọn.';
                            alertBox.style.display = 'block';
                            if (firstErrorInput) firstErrorInput.focus();
                            e.preventDefault();
                            return false;
                        }
                        return true;
                    }
                    </script>

                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold text-primary">2. Thời hạn bổ sung trực tuyến <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="han_bo_sung" class="form-control" value="{{ $dk->han_bo_sung ? $dk->han_bo_sung->format('Y-m-d\TH:i') : now()->addDays(2)->format('Y-m-d\TH:i') }}" required>
                        <small class="text-muted">Hệ thống sẽ tự động khóa tính năng bổ sung trực tuyến khi hết thời hạn trên.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning px-4">Xác nhận Gửi yêu cầu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. MODAL TỪ CHỐI HỒ SƠ -->
    <div class="modal fade" id="tuChoi{{ $dk->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.dangky.reject', [$lichthi, $dk]) }}" class="modal-content">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Từ chối hồ sơ đăng ký</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lý do từ chối hồ sơ <span class="text-danger">*</span></label>
                        <textarea name="ly_do_tu_choi" class="form-control" rows="4" placeholder="Nhập chi tiết lý do từ chối đăng ký..." required></textarea>
                    </div>
                    <p class="text-muted small mb-0">Hồ sơ sau khi bị từ chối sẽ không được xếp lịch dự thi và email thông báo kèm lý do sẽ được gửi cho sinh viên.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger px-4">Xác nhận Từ chối</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. MODAL HỖ TRỢ BỔ SUNG HỒ SƠ QUÁ HẠN -->
    <div class="modal fade" id="hoTroBoSung{{ $dk->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form method="POST" action="{{ route('admin.dangky.hotrobosung', [$lichthi, $dk]) }}" enctype="multipart/form-data" class="modal-content" novalidate onsubmit="return confirmHoTroBoSung_{{ $dk->id }}(event)">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">🛠️ Hỗ trợ bổ sung hồ sơ quá hạn — {{ $dk->sinhVien->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-info py-2 mb-3 small">
                        <strong>📌 Lưu ý cán bộ:</strong> Hồ sơ đã hết thời hạn tự bổ sung trực tuyến. Cán bộ Phòng Khảo thí thực hiện cập nhật thông tin/giấy tờ thay cho sinh viên dựa trên hồ sơ minh chứng nộp trực tiếp.
                    </div>

                    @if ($dk->ly_do_bo_sung)
                        <div class="alert alert-warning py-2 mb-3 small">
                            <strong class="d-block mb-1">⚠️ Nội dung Admin đã từng yêu cầu bổ sung trước đây:</strong>
                            <div style="white-space: pre-line;">{{ $dk->ly_do_bo_sung }}</div>
                        </div>
                    @endif

                    <div id="hoTroBoSungAlert_{{ $dk->id }}" class="alert alert-danger py-2 mb-3 small fw-medium" style="display:none">
                        Vui lòng nhập lý do bổ sung sau thời hạn.
                    </div>

                    <!-- THÔNG TIN CÁ NHÂN & LIÊN HỆ -->
                    <h6 class="fw-bold text-primary mb-3">1. Thông tin cá nhân & Liên hệ</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Số điện thoại liên hệ</label>
                            <input type="text" name="so_dien_thoai" class="form-control form-control-sm" value="{{ old('so_dien_thoai', $dk->so_dien_thoai) }}" placeholder="10 chữ số">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" class="form-control form-control-sm" value="{{ old('ngay_sinh', optional($dk->ngay_sinh)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Giới tính</label>
                            <select name="gioi_tinh" class="form-select form-select-sm">
                                <option value="nam" {{ old('gioi_tinh', $dk->gioi_tinh) === 'nam' ? 'selected' : '' }}>Nam</option>
                                <option value="nu" {{ old('gioi_tinh', $dk->gioi_tinh) === 'nu' ? 'selected' : '' }}>Nữ</option>
                                <option value="khac" {{ old('gioi_tinh', $dk->gioi_tinh) === 'khac' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Dân tộc</label>
                            <input type="text" name="dan_toc" class="form-control form-control-sm" value="{{ old('dan_toc', $dk->dan_toc) }}" placeholder="Kinh, Tày, Nùng...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nơi sinh</label>
                            <input type="text" name="noi_sinh" class="form-control form-control-sm" value="{{ old('noi_sinh', $dk->noi_sinh) }}" placeholder="Tỉnh/Thành phố nơi sinh">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Số CCCD / Định danh</label>
                            <input type="text" name="so_cccd" class="form-control form-control-sm" value="{{ old('so_cccd', $dk->so_cccd) }}" placeholder="12 chữ số">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email liên hệ</label>
                            <input type="email" name="email_lien_he" class="form-control form-control-sm" value="{{ old('email_lien_he', $dk->email_lien_he) }}" placeholder="Email nhận thông báo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Địa chỉ chi tiết (Số nhà, đường...)</label>
                            <input type="text" name="dia_chi_chi_tiet" class="form-control form-control-sm" value="{{ old('dia_chi_chi_tiet', $dk->dia_chi_chi_tiet) }}" placeholder="Số nhà, tên đường">
                        </div>
                    </div>

                    <!-- HÌNH ẢNH / GIẤY TỜ MINH CHỨNG -->
                    <h6 class="fw-bold text-primary mb-3">2. Tải lên / Thay thế tệp & hình ảnh minh chứng</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ảnh CCCD Mặt trước</label>
                            @if ($dk->anh_cccd_truoc)
                                <div class="mb-1"><a href="{{ Storage::url($dk->anh_cccd_truoc) }}" target="_blank" class="small text-decoration-none fw-medium">🔍 Xem ảnh hiện tại</a></div>
                            @endif
                            <input type="file" name="anh_cccd_truoc" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ảnh CCCD Mặt sau</label>
                            @if ($dk->anh_cccd_sau)
                                <div class="mb-1"><a href="{{ Storage::url($dk->anh_cccd_sau) }}" target="_blank" class="small text-decoration-none fw-medium">🔍 Xem ảnh hiện tại</a></div>
                            @endif
                            <input type="file" name="anh_cccd_sau" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ảnh Hồ sơ 3x4</label>
                            @if ($dk->anh_ho_so)
                                <div class="mb-1"><a href="{{ Storage::url($dk->anh_ho_so) }}" target="_blank" class="small text-decoration-none fw-medium">🔍 Xem ảnh hiện tại</a></div>
                            @endif
                            <input type="file" name="anh_ho_so" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ảnh Thẻ Sinh viên</label>
                            @if ($dk->anh_the_sv)
                                <div class="mb-1"><a href="{{ Storage::url($dk->anh_the_sv) }}" target="_blank" class="small text-decoration-none fw-medium">🔍 Xem ảnh hiện tại</a></div>
                            @endif
                            <input type="file" name="anh_the_sv" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>

                    <!-- THÔNG TIN BẮT BỘC & GHI CHÚ -->
                    <h6 class="fw-bold text-primary mb-3">3. Lý do bổ sung & Ghi chú cán bộ</h6>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lý do bổ sung sau thời hạn <span class="text-danger">*</span></label>
                        <textarea id="ly_do_bo_sung_qua_han_{{ $dk->id }}" name="ly_do_bo_sung_qua_han" class="form-control" rows="2" placeholder="Nhập lý do bổ sung sau thời hạn (bắt buộc)..." required oninput="this.classList.remove('is-invalid'); var a = document.getElementById('hoTroBoSungAlert_{{ $dk->id }}'); if(a) a.style.display='none';"></textarea>
                        <small class="text-muted">Lý do này bắt buộc phải nhập để lưu lịch sử thao tác.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ghi chú của cán bộ <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                        <textarea name="ghi_chu_can_bo" class="form-control" rows="2" placeholder="Nhập ghi chú xử lý bổ sung nếu có..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Xác nhận Bổ sung</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function confirmHoTroBoSung_{{ $dk->id }}(e) {
        var txtReason = document.getElementById('ly_do_bo_sung_qua_han_{{ $dk->id }}');
        var alertBox = document.getElementById('hoTroBoSungAlert_{{ $dk->id }}');

        if (!txtReason || !txtReason.value.trim()) {
            if (alertBox) {
                alertBox.innerText = 'Vui lòng nhập lý do bổ sung sau thời hạn.';
                alertBox.style.display = 'block';
            }
            if (txtReason) {
                txtReason.classList.add('is-invalid');
                txtReason.focus();
            }
            e.preventDefault();
            return false;
        }

        if (alertBox) alertBox.style.display = 'none';

        var ok = confirm('Bạn có chắc chắn muốn bổ sung hồ sơ thay cho sinh viên này không?');
        if (!ok) {
            e.preventDefault();
            return false;
        }
        return true;
    }
    </script>

@endforeach

@endsection
