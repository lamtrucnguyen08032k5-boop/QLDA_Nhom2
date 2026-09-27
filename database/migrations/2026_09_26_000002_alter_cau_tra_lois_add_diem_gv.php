<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cau_tra_lois', function (Blueprint $table) {
            $table->decimal('diem_gv1', 5, 2)->nullable()->after('bai_lam_tu_luan');
            $table->decimal('diem_gv2', 5, 2)->nullable()->after('diem_gv1');
        });
    }

    public function down(): void
    {
        Schema::table('cau_tra_lois', function (Blueprint $table) {
            $table->dropColumn(['diem_gv1', 'diem_gv2']);
        });
    }
};
