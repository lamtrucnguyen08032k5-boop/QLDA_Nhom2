@extends('layouts.app')
@section('title', 'Tiến độ chấm thi (Khoa)')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3"><i class="bi bi-speedometer2 me-2"></i>Theo dõi tiến độ chấm thi 2 vòng</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Mã bài thi</th>
                        <th>Sinh viên</th>
                        <th>Kỳ thi / Đề thi</th>
                        <th>Thời gian nộp</th>
                        <th>GV chấm lần 1</th>
                        <th>GV chấm lần 2</th>
                        <th class="text-center">Trạng thái chấm</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($baiThis as $bt)
                    <tr>
                        <td>
                            <span class="font-monospace fw-semibold text-primary">{{ $bt->ma_bai_thi_hien_thi }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $bt->dangKy->sinhVien->name ?? 'N/A' }}</div>
                            <span class="text-muted small">{{ $bt->dangKy->sinhVien->ma_so ?? '' }}</span>
                        </td>
                        <td>
                            <div>{{ $bt->dangKy->lichThi->ten_ky_thi ?? ($bt->deThi?->ten_de ?? '—') }}</div>
                            <span class="text-muted small">Phòng: {{ $bt->dangKy->lichThi->phong_thi ?? '—' }}</span>
                        </td>
                        <td>{{ $bt->gio_nop?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>
                            @if($bt->giangVien1)
                                <span class="fw-semibold">{{ $bt->giangVien1->name }}</span>
                                @if($bt->ngay_cham_1)
                                    <div class="text-success small"><i class="bi bi-check me-1"></i>Đã chấm xong</div>
                                @elseif(in_array($bt->trang_thai, ['cho_cham_1', 'dang_cham_1']))
                                    <div class="text-warning small"><i class="bi bi-clock me-1"></i>Đang chấm</div>
                                @endif
                            @else
                                <span class="text-muted fst-italic">Chưa phân công</span>
                            @endif
                        </td>
                        <td>
                            @if($bt->giangVien2)
                                <span class="fw-semibold">{{ $bt->giangVien2->name }}</span>
                                @if($bt->ngay_cham_2)
                                    <div class="text-success small"><i class="bi bi-check me-1"></i>Đã chấm xong</div>
                                @elseif(in_array($bt->trang_thai, ['cho_cham_2', 'dang_cham_2']))
                                    <div class="text-warning small"><i class="bi bi-clock me-1"></i>Đang chấm</div>
                                @endif
                            @else
                                <span class="text-muted fst-italic">Chưa phân công</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @switch($bt->trang_thai)
                                @case('da_nop')
                                    <span class="badge bg-secondary">Chờ phân công GV</span>
                                    @break
                                @case('cho_cham_1')
                                @case('dang_cham_1')
                                    <span class="badge bg-warning text-dark">Đang chấm vòng 1</span>
                                    @break
                                @case('cho_cham_2')
                                @case('dang_cham_2')
                                    <span class="badge bg-info text-dark">Đang chấm vòng 2</span>
                                    @break
                                @case('cho_thong_nhat')
                                    <span class="badge bg-danger">Chờ thống nhất điểm</span>
                                    @break
                                @case('da_chot')
                                    <span class="badge bg-primary">Đã chốt điểm ({{ $bt->diem_tong }})</span>
                                    @break
                                @case('da_cong_bo')
                                    <span class="badge bg-success">Đã công bố ({{ $bt->diem_tong }})</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $bt->trang_thai }}</span>
                            @endswitch
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có bài thi nào nộp bài.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">
    {{ $baiThis->links() }}
</div>
@endsection
