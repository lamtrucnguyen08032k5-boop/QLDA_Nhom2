@extends('layouts.app')
@section('title', 'Tổ chức thi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-1">Các ca thi sẵn sàng tổ chức</h5>
        <p class="text-muted small mb-0">Quản lý bắt đầu, kết thúc ca thi và lấy mã ca thi để cấp cho thí sinh vào phòng thi</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-3" style="width: 140px;">Mã ca thi</th>
                        <th>Kỳ thi</th>
                        <th>Khoa</th>
                        <th>Ngày thi</th>
                        <th>Phòng</th>
                        <th>Giờ thi</th>
                        <th class="text-center">Số TS đã duyệt</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($lichThis as $lt)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace fs-6 px-2 py-1 user-select-all" title="Mã ca thi cấp cho thí sinh để vào phòng">
                                    {{ $lt->ma_ca_thi ?? 'N/A' }}
                                </span>
                                @if($lt->ma_ca_thi)
                                    <button type="button" class="btn btn-sm btn-light border p-1 text-secondary" title="Sao chép mã" onclick="navigator.clipboard.writeText('{{ $lt->ma_ca_thi }}'); this.innerHTML='<i class=\'bi bi-check text-success\'></i>'; setTimeout(() => this.innerHTML='<i class=\'bi bi-clipboard\'></i>', 2000);">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $lt->ten_ky_thi }}</div>
                            @if($lt->deThi)
                                <div class="text-muted small">Đề: {{ $lt->deThi->ten_de }}</div>
                            @endif
                        </td>
                        <td>{{ $lt->khoa->ten_khoa ?? 'N/A' }}</td>
                        <td>{{ $lt->ngay_thi ? $lt->ngay_thi->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $lt->phong_thi }}</span></td>
                        <td><span class="fw-semibold text-primary"><i class="bi bi-clock me-1"></i>{{ $lt->gio_bat_dau ? \Carbon\Carbon::parse($lt->gio_bat_dau)->format('H:i') : '—' }}</span></td>
                        <td class="text-center"><span class="badge rounded-pill bg-info bg-opacity-10 text-info-emphasis border px-2 py-1">{{ $lt->so_thi_sinh }} TS</span></td>
                        <td class="text-center">
                            @if ($lt->trang_thai === 'dang_thi')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                    <span class="spinner-grow spinner-grow-sm me-1" role="status" style="width: 0.45rem; height: 0.45rem;"></span>Đang thi
                                </span>
                            @elseif ($lt->trang_thai === 'da_dong_dang_ky')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">Đã đóng ĐK</span>
                            @else
                                <span class="badge text-bg-secondary">{{ $lt->trang_thai }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-3 text-nowrap">
                            @if ($lt->trang_thai === 'da_dong_dang_ky')
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#batDau{{ $lt->id }}">
                                    <i class="bi bi-play-circle me-1"></i>Bắt đầu ca thi
                                </button>
                                <div class="modal fade text-start" id="batDau{{ $lt->id }}">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('admin.tochuc.batdau', $lt) }}" class="modal-content">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Bắt đầu ca thi phòng {{ $lt->phong_thi }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info py-2 small mb-3">
                                                    <i class="bi bi-info-circle me-1"></i>Mã ca thi để cấp cho thí sinh: <strong class="font-monospace text-primary fs-6">{{ $lt->ma_ca_thi }}</strong>
                                                </div>
                                                <label class="form-label fw-semibold">Chọn đề thi áp dụng <span class="text-danger">*</span></label>
                                                <select name="de_thi_id" class="form-select" required>
                                                    <option value="">-- Chọn đề thi --</option>
                                                    @foreach (\App\Models\DeThi::where('khoa_id', $lt->khoa_id)->where('loai_chung_chi', $lt->loai_chung_chi)->where('active', true)->get() as $de)
                                                        <option value="{{ $de->id }}">{{ $de->ma_de }} - {{ $de->ten_de }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                <button class="btn btn-primary btn-sm"><i class="bi bi-play-fill me-1"></i>Bắt đầu</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @elseif ($lt->trang_thai === 'dang_thi')
                                <form method="POST" action="{{ route('admin.tochuc.ketthuc', $lt) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn kết thúc ca thi này?');">
                                        <i class="bi bi-stop-circle me-1"></i>Kết thúc ca thi
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Hiện không có ca thi nào sẵn sàng tổ chức.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">
    {{ $lichThis->links() }}
</div>
@endsection
