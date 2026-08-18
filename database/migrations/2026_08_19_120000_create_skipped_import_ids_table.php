<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skipped_import_ids', function (Blueprint $table) {
            $table->id();
            $table->string('host_id')->index();
            $table->string('report_type')->index();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->string('source_file_name')->nullable();
            $table->foreignId('imported_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skipped_import_ids');
    }
};