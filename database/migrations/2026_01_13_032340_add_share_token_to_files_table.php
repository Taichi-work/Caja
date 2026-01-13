<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            // 共有用トークンと有効期限のカラムを追加
            $table->string('share_token')->nullable()->unique()->after('mime_type');
            $table->timestamp('shared_expires_at')->nullable()->after('share_token');
        });
    }

    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['share_token', 'shared_expires_at']);
        });
    }
};