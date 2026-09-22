<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bai_this', function (Blueprint $table) {
            $table->string('ma_bai_thi')->nullable()->unique()->after('id');
            $table->timestamp('ngay_cong_bo')->nullable()->after('ngay_cham');
            $table->foreignId('nguoi_cong_bo_id')->nullable()->after('ngay_cong_bo')->constrained('users')->nullOnDelete();
        });

        Schema::table('lich_this', function (Blueprint $table) {
            $table->string('trang_thai_cong_bo')->default('chua_cong_bo')->after('trang_thai'); // chua_cong_bo | da_cong_bo
            $table->timestamp('ngay_cong_bo')->nullable()->after('trang_thai_cong_bo');
            $table->foreignId('nguoi_cong_bo_id')->nullable()->after('ngay_cong_bo')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lich_this', function (Blueprint $table) {
            $table->dropForeign(['nguoi_cong_bo_id']);
            $table->dropColumn(['trang_thai_cong_bo', 'ngay_cong_bo', 'nguoi_cong_bo_id']);
        });

        Schema::table('bai_this', function (Blueprint $table) {
            $table->dropForeign(['nguoi_cong_bo_id']);
            $table->dropColumn(['ma_bai_thi', 'ngay_cong_bo', 'nguoi_cong_bo_id']);
        });
    }
};
