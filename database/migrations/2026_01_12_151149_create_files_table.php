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
        Schema::create('files', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->cascadeOnDelete(); // 誰のファイルか
            $blueprint->string('original_name'); // 元のファイル名
            $blueprint->string('storage_path');  // ストレージ内の保存パス
            $blueprint->bigInteger('size');      // ファイルサイズ
            $blueprint->string('mime_type');     // ファイル形式（image/pngなど）
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
