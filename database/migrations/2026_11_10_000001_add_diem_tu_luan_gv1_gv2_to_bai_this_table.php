<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bai_this', function (Blueprint $table) {
            $table->decimal('diem_tu_luan_gv1', 5, 2)->nullable()->after('nhan_xet_1');
            $table->decimal('diem_tu_luan_gv2', 5, 2)->nullable()->after('nhan_xet_2');
        });
    }

    public function down(): void
    {
        Schema::table('bai_this', function (Blueprint $table) {
            $table->dropColumn(['diem_tu_luan_gv1', 'diem_tu_luan_gv2']);
        });
    }
};

