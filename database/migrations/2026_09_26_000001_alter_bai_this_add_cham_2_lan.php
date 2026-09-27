<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bai_this', function (Blueprint $table) {
            $table->foreignId('giang_vien_1_id')->nullable()->after('giang_vien_id')->constrained('users')->nullOnDelete();
            $table->foreignId('giang_vien_2_id')->nullable()->after('giang_vien_1_id')->constrained('users')->nullOnDelete();
            $table->text('nhan_xet_1')->nullable()->after('giang_vien_2_id');
            $table->text('nhan_xet_2')->nullable()->after('nhan_xet_1');
            $table->timestamp('ngay_cham_1')->nullable()->after('nhan_xet_2');
            $table->timestamp('ngay_cham_2')->nullable()->after('ngay_cham_1');
            $table->decimal('diem_chot', 5, 2)->nullable()->after('ngay_cham_2');
            $table->text('ly_do_thong_nhat')->nullable()->after('diem_chot');
            $table->boolean('da_khoa')->default(false)->after('ly_do_thong_nhat');
        });
    }

    public function down(): void
    {
        Schema::table('bai_this', function (Blueprint $table) {
            $table->dropForeign(['giang_vien_1_id']);
            $table->dropForeign(['giang_vien_2_id']);
            $table->dropColumn([
                'giang_vien_1_id',
                'giang_vien_2_id',
                'nhan_xet_1',
                'nhan_xet_2',
                'ngay_cham_1',
                'ngay_cham_2',
                'diem_chot',
                'ly_do_thong_nhat',
                'da_khoa',
            ]);
        });
    }
};
