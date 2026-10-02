<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sv_whitelists', function (Blueprint $table) {
            if (!Schema::hasColumn('sv_whitelists', 'khoa_id')) {
                $table->foreignId('khoa_id')->nullable()->after('khoa_hoc')->constrained('khoas')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sv_whitelists', function (Blueprint $table) {
            if (Schema::hasColumn('sv_whitelists', 'khoa_id')) {
                $table->dropConstrainedForeignId('khoa_id');
            }
        });
    }
};
