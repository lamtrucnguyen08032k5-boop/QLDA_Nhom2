<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chung_nhans', function (Blueprint $table) {
            $table->foreignId('nguoi_cap_id')->nullable()->after('sinh_vien_id')->constrained('users')->nullOnDelete();
            $table->text('ghi_chu')->nullable()->after('file_chung_nhan');
        });
    }

    public function down(): void
    {
        Schema::table('chung_nhans', function (Blueprint $table) {
            $table->dropForeign(['nguoi_cap_id']);
            $table->dropColumn(['nguoi_cap_id', 'ghi_chu']);
        });
    }
};
