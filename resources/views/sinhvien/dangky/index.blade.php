@extends('layouts.sinhvien')
@section('title', 'Đăng ký thi')
@section('content')
<form class="mb-4 d-flex align-items-center gap-2">
    <span class="fw-semibold text-primary">Lọc theo:</span>
    <select name="loai_chung_chi" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
        <option value="">-- Tất cả loại chứng chỉ --</option>
        <option value="cntt" {{ request('loai_chung_chi') === 'cntt' ? 'selected' : '' }}>CNTT</option>
        <option value="tienganh" {{ request('loai_chung_chi') === 'tienganh' ? 'selected' : '' }}>Tiếng Anh</option>
    </select>
</form>
<div class="row g-3">
    @forelse ($lichThis as $lt)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header-blue">{{ $lt->loai_chung_chi === 'cntt' ? 'Chứng chỉ CNTT' : 'Chứng chỉ Tiếng Anh' }}</div>
                <div class="card-body d-flex flex-column">
                    <h6 class="fw-bold">{{ $lt->ten_ky_thi }}</h6>
                    <p class="small text-muted mb-1">📅 Ngày thi: {{ $lt->ngay_thi->format('d/m/Y') }} · Phòng {{ $lt->phong_thi }}</p>
                    <p class="small text-muted mb-1">⏰ Hạn đăng ký: {{ $lt->han_dang_ky->format('d/m/Y H:i') }}</p>
                    <p class="small mb-2 {{ $lt->so_cho_con_lai > 0 ? 'text-success fw-semibold' : 'text-danger fw-bold' }}">
                        👥 Còn chỗ: {{ $lt->so_cho_con_lai }} / {{ $lt->so_luong_toi_da }}
                    </p>
                    <p class="fw-bold mb-3" style="color:var(--hvnh-orange)">{{ number_format($lt->le_phi) }}đ</p>
                    <div class="mt-auto">
                        @if (in_array($lt->id, $daDangKyIds))
                            <span class="badge badge-cho-duyet w-100 py-2">Đã đăng ký</span>
                        @elseif ($lt->daHetCho())
                            <button class="btn btn-secondary w-100" disabled>Hết chỗ</button>
                        @else
                            <a href="{{ route('sinhvien.dangky.buoc1', $lt) }}" class="btn btn-primary w-100">Đăng ký dự thi</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Hiện không có ca thi nào đang mở đăng ký.</p>
    @endforelse
</div>
<div class="mt-3">{{ $lichThis->links() }}</div>
@endsection
