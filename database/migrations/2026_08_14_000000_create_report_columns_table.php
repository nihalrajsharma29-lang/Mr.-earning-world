<?php

use App\Support\ReportColumnManager;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_columns', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->string('column_key');
            $table->string('label');
            $table->string('type')->default('text');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->unique(['report_type', 'column_key']);
        });

        foreach (ReportColumnManager::all() as $reportType => $columns) {
            foreach ($columns as $position => $column) {
                DB::table('report_columns')->insert([
                    'report_type' => $reportType,
                    'column_key' => $column['key'],
                    'label' => $column['label'],
                    'type' => $column['type'] ?? 'text',
                    'position' => $position,
                    'is_visible' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('report_columns');
    }
};