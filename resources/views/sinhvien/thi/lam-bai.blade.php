@extends('layouts.sinhvien')
@section('title', 'Làm bài thi')
@section('content')
<div class="alert alert-info d-flex justify-content-between">
    <span>Hạn nộp bài: {{ $hanNop->format('H:i d/m/Y') }}</span>
    <span id="dem-nguoc" class="fw-bold"></span>
</div>
<form method="POST" action="{{ route('sinhvien.thi.nop-bai', $baithi) }}" id="form-thi">
    @csrf
    @foreach ($baithi->deThi->cauHois as $cauHoi)
        <div class="card mb-3"><div class="card-body">
            <p class="fw-semibold">Câu {{ $loop->iteration }} ({{ $cauHoi->diem }} điểm)</p>
            <p>{{ $cauHoi->noi_dung }}</p>
            @if ($cauHoi->loai_cau === 'tracnghiem')
                @foreach (['A' => $cauHoi->dap_an_a, 'B' => $cauHoi->dap_an_b, 'C' => $cauHoi->dap_an_c, 'D' => $cauHoi->dap_an_d] as $key => $val)
                    @if ($val)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tra_loi[{{ $cauHoi->id }}]" value="{{ $key }}" id="c{{ $cauHoi->id }}{{ $key }}">
                            <label class="form-check-label" for="c{{ $cauHoi->id }}{{ $key }}">{{ $key }}. {{ $val }}</label>
                        </div>
                    @endif
                @endforeach
            @else
                <textarea name="tra_loi[{{ $cauHoi->id }}]" class="form-control" rows="4" placeholder="Nhập câu trả lời..."></textarea>
            @endif
        </div></div>
    @endforeach
    <button type="submit" class="btn btn-primary" data-confirm="Bạn có chắc chắn muốn nộp bài?">Nộp bài</button>
</form>
@endsection

@section('scripts')
<script>
const hanNop = new Date("{{ $hanNop->toIso8601String() }}").getTime();
const el = document.getElementById('dem-nguoc');
const timer = setInterval(() => {
    const now = new Date().getTime();
    const dist = hanNop - now;
    if (dist <= 0) {
        clearInterval(timer);
        el.textContent = 'Đã hết giờ - đang tự động nộp bài...';
        document.getElementById('form-thi').submit();
        return;
    }
    const m = Math.floor(dist / 60000);
    const s = Math.floor((dist % 60000) / 1000);
    el.textContent = `Còn lại: ${m} phút ${s} giây`;
}, 1000);
</script>
@endsection
