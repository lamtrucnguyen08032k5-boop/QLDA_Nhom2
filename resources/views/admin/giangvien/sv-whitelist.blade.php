@extends('layouts.app')
@section('title', 'Kho email Sinh viên')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold text-primary mb-1">Kho Email Sinh Viên Hợp Lệ</h4>
        <p class="text-muted mb-0 small">Danh sách sinh viên được phép đăng ký và tạo tài khoản tham gia thi chứng chỉ</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-success btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importSvModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i>Import Sinh viên (CSV)
        </button>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSvModal">
            <i class="bi bi-person-plus-fill me-1"></i>Thêm sinh viên
        </button>
    </div>
</div>

{{-- Thanh Tìm kiếm & Lọc theo Khoa --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3 bg-light rounded-3">
        <form method="GET" action="{{ route('admin.svwhitelist.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-secondary mb-1">Tìm kiếm sinh viên:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input name="search" class="form-control border-start-0" placeholder="Nhập mã SV, họ tên, email, lớp..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary mb-1">Khoa / Đơn vị:</label>
                <select name="khoa_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả các Khoa --</option>
                    @foreach($khoas as $k)
                        <option value="{{ $k->id }}" {{ request('khoa_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->ten_khoa }} ({{ $k->ma_khoa }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Lọc dữ liệu
                    </button>
                    @if(request()->filled('search') || request()->filled('khoa_id'))
                        <a href="{{ route('admin.svwhitelist.index') }}" class="btn btn-outline-secondary btn-sm" title="Bỏ lọc">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Bảng danh sách Sinh viên --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0">
            <i class="bi bi-list-check me-2 text-primary"></i>Danh sách sinh viên trong kho email
        </h6>
        <span class="badge bg-light text-secondary border">Tổng: {{ $sinhViens->total() }} sinh viên</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 130px;">Mã SV</th>
                        <th>Họ và tên</th>
                        <th>Email trường</th>
                        <th>Khoa / Đơn vị</th>
                        <th>Lớp niên chế</th>
                        <th>Khóa học</th>
                        <th class="text-center" style="width: 140px;">Trạng thái</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($sinhViens as $sv)
                    <tr>
                        <td class="ps-3 fw-semibold text-primary">{{ $sv->ma_sv }}</td>
                        <td class="fw-bold text-dark">{{ $sv->ho_ten }}</td>
                        <td>{{ $sv->email }}</td>
                        <td>
                            @if($sv->khoa)
                                <span class="badge bg-light text-dark border">{{ $sv->khoa->ten_khoa }}</span>
                            @else
                                <span class="text-muted small fst-italic">Chưa phân khoa</span>
                            @endif
                        </td>
                        <td>{{ $sv->lop ?: '—' }}</td>
                        <td>{{ $sv->khoa_hoc ?: '—' }}</td>
                        <td class="text-center">
                            @if($sv->da_dang_ky)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i>Đã đăng ký
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                    <i class="bi bi-hourglass-split me-1"></i>Chưa đăng ký
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <button type="button" class="btn btn-link p-0 text-secondary border-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#editSvModal{{ $sv->id }}" title="Chỉnh sửa thông tin sinh viên" style="font-size: 1.15rem; line-height: 1; transition: color 0.15s;" onmouseover="this.style.color='#0d6efd'" onmouseout="this.style.color=''">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.svwhitelist.destroy', $sv) }}" class="d-inline m-0" data-confirm="Bạn có chắc muốn xoá sinh viên &quot;{{ $sv->ho_ten }} ({{ $sv->ma_sv }})&quot; khỏi danh sách?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 text-secondary border-0 text-decoration-none" title="Xoá sinh viên" style="font-size: 1.15rem; line-height: 1; transition: color 0.15s;" onmouseover="this.style.color='#dc3545'" onmouseout="this.style.color=''">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Modal Chỉnh sửa Sinh viên --}}
                            <div class="modal fade text-start" id="editSvModal{{ $sv->id }}" tabindex="-1" aria-labelledby="editSvModalLabel{{ $sv->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.svwhitelist.update', $sv) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold" id="editSvModalLabel{{ $sv->id }}">
                                                    <i class="bi bi-pencil-square text-primary me-1"></i>Chỉnh sửa thông tin sinh viên
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Mã sinh viên <span class="text-danger">*</span></label>
                                                        <input name="ma_sv" class="form-control" value="{{ old('ma_sv', $sv->ma_sv) }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                                        <input name="ho_ten" class="form-control" value="{{ old('ho_ten', $sv->ho_ten) }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-semibold">Email trường <span class="text-danger">*</span></label>
                                                        <input type="email" name="email" class="form-control" value="{{ old('email', $sv->email) }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-semibold">Khoa / Đơn vị quản lý <span class="text-danger">*</span></label>
                                                        <select name="khoa_id" class="form-select" required>
                                                            <option value="">-- Chọn Khoa --</option>
                                                            @foreach($khoas as $k)
                                                                <option value="{{ $k->id }}" {{ old('khoa_id', $sv->khoa_id) == $k->id ? 'selected' : '' }}>
                                                                    {{ $k->ten_khoa }} ({{ $k->ma_khoa }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Lớp niên chế</label>
                                                        <input name="lop" class="form-control" value="{{ old('lop', $sv->lop) }}" placeholder="VD: K24CLCA">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Khóa học</label>
                                                        <input name="khoa_hoc" class="form-control" value="{{ old('khoa_hoc', $sv->khoa_hoc) }}" placeholder="VD: K24">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-save me-1"></i>Lưu thay đổi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            Không tìm thấy sinh viên nào phù hợp với điều kiện tìm kiếm.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">
    @include('partials.pagination', ['paginator' => $sinhViens])
</div>

{{-- Modal Thêm 1 Sinh viên --}}
<div class="modal fade" id="addSvModal" tabindex="-1" aria-labelledby="addSvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.svwhitelist.store') }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="addSvModalLabel">
                        <i class="bi bi-person-plus-fill text-primary me-1"></i>Thêm sinh viên mới
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Mã sinh viên <span class="text-danger">*</span></label>
                            <input name="ma_sv" class="form-control" placeholder="VD: 24A4040123" value="{{ old('ma_sv') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input name="ho_ten" class="form-control" placeholder="VD: Nguyễn Văn A" value="{{ old('ho_ten') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Email trường <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="VD: 24a4040123@hvnh.edu.vn" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Khoa / Đơn vị quản lý <span class="text-danger">*</span></label>
                            <select name="khoa_id" class="form-select" required>
                                <option value="">-- Chọn Khoa --</option>
                                @foreach($khoas as $k)
                                    <option value="{{ $k->id }}" {{ old('khoa_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->ten_khoa }} ({{ $k->ma_khoa }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Lớp niên chế</label>
                            <input name="lop" class="form-control" placeholder="VD: K24CLCA" value="{{ old('lop') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Khóa học</label>
                            <input name="khoa_hoc" class="form-control" placeholder="VD: K24" value="{{ old('khoa_hoc') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Thêm sinh viên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Import Sinh viên (CSV) --}}
<div class="modal fade" id="importSvModal" tabindex="-1" aria-labelledby="importSvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.svwhitelist.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="importSvModalLabel">
                        <i class="bi bi-file-earmark-arrow-up text-success me-1"></i>Import danh sách Sinh viên từ file CSV
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        Chọn file CSV chứa danh sách sinh viên hợp lệ. Hệ thống sẽ tự động thêm vào kho email cho phép đăng ký tài khoản.
                    </p>
                    <div class="alert alert-info py-2 small mb-3">
                        <strong>Cấu trúc tiêu đề cột trong file CSV:</strong><br>
                        <code>Mã SV,Họ tên,Email,Lớp niên chế,Khóa học,Mã khoa</code><br>
                        <span class="text-muted fst-italic">(Cột "Mã khoa" có thể điền mã khoa ví dụ: CNTT, NNH, NGANHANG...)</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Chọn file CSV (.csv, .txt) <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.svwhitelist.sample') }}" class="btn btn-sm btn-link text-decoration-none">
                            <i class="bi bi-download me-1"></i>Tải file mẫu (.csv)
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-cloud-arrow-up me-1"></i>Tiến hành Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
