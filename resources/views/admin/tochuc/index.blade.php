@extends('layouts.app')
@section('title', 'Tổ chức thi')
@section('content')
<h5 class="mb-3">Các ca thi sẵn sàng tổ chức</h5>
<table class="table table-bordered bg-white">
    <thead><tr><th>Kỳ thi</th><th>Khoa</th><th>Ngày thi</th><th>Phòng</th><th>Giờ thi</th><th>Số TS đã duyệt</th><th>Trạng thái</th><th></th></tr></thead>
    <tbody>
    @foreach ($lichThis as $lt)
        <tr>
            <td>{{ $lt->ten_ky_thi }}</td>
            <td>{{ $lt->khoa->ten_khoa }}</td>
            <td>{{ $lt->ngay_thi->format('d/m/Y') }}</td>
            <td>{{ $lt->phong_thi }}</td>
            <td><span class="fw-semibold text-primary"><i class="bi bi-clock me-1"></i>{{ $lt->gio_bat_dau ? \Carbon\Carbon::parse($lt->gio_bat_dau)->format('H:i') : '—' }}</span></td>
            <td>{{ $lt->so_thi_sinh }}</td>
            <td><span class="badge text-bg-secondary">{{ $lt->trang_thai }}</span></td>
            <td class="text-nowrap">
                @if ($lt->trang_thai === 'da_dong_dang_ky')
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#batDau{{ $lt->id }}">Bắt đầu ca thi</button>
                    <div class="modal fade" id="batDau{{ $lt->id }}">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.tochuc.batdau', $lt) }}" class="modal-content">
                                @csrf
                                <div class="modal-body">
                                    <label class="form-label">Chọn đề thi áp dụng</label>
                                    <select name="de_thi_id" class="form-select" required>
                                        <option value="">-- Chọn đề thi --</option>
                                        @foreach (\App\Models\DeThi::where('khoa_id', $lt->khoa_id)->where('loai_chung_chi', $lt->loai_chung_chi)->where('active', true)->get() as $de)
                                            <option value="{{ $de->id }}">{{ $de->ma_de }} - {{ $de->ten_de }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button class="btn btn-primary">Bắt đầu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif ($lt->trang_thai === 'dang_thi')
                    <form method="POST" action="{{ route('admin.tochuc.ketthuc', $lt) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">Kết thúc ca thi</button>
                    </form>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $lichThis->links() }}
@endsection
