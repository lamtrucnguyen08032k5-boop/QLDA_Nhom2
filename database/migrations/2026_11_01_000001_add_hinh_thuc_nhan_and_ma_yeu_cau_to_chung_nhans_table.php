<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chung_nhans', function (Blueprint $table) {
            if (!Schema::hasColumn('chung_nhans', 'hinh_thuc_nhan')) {
                $table->string('hinh_thuc_nhan')->default('truc_tiep')->after('trang_thai');
            }
            if (!Schema::hasColumn('chung_nhans', 'ma_yeu_cau')) {
                $table->string('ma_yeu_cau')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chung_nhans', function (Blueprint $table) {
            $table->dropColumn(['hinh_thuc_nhan', 'ma_yeu_cau']);
        });
    }
};

