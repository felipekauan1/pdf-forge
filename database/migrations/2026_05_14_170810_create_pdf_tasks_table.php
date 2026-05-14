<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdf_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('operation');  // ex: merge, split, compress
            $table->string('status')->default('pending');  // pending, processing, done, failed
            $table->string('original_filename');
            $table->string('result_path')->nullable();  // caminho do arquivo gerado
            $table->text('error_message')->nullable();  // se algo der errado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_tasks');
    }
};
