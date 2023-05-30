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
        Schema::create('csv_export_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('download_user_id')->comment('ダウンロードユーザーID');
            $table->foreign('download_user_id')->references('id')->on('users');
            $table->string('file_name')->comment('ファイル名');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_export_histories');
    }
};
