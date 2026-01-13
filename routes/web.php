<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. トップページ：ログインしていれば一覧へ、そうでなければWelcomeへ
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('files.index');
    }
    return view('welcome');
});

// 2. 共有リンク用（ログイン不要：authミドルウェアの外側に配置）
// ここに書くことで、ログインしていない外部の人もアクセス可能になります
Route::get('/shared/{token}', [FileController::class, 'showShared'])->name('files.shared');
Route::get('/shared/{token}/download', [FileController::class, 'sharedDownload'])->name('files.shared.download');


// 3. ログイン認証が必要なルート
Route::middleware('auth')->group(function () {
    
    // --- Caja: ファイル管理機能 ---
    
    // ゴミ箱（論理削除済み）の一覧表示
    Route::get('/files/trash', [FileController::class, 'trash'])->name('files.trash');
    
    // 通常のCRUD（一覧、保存、ダウンロード、編集、更新、論理削除）
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/edit', [FileController::class, 'edit'])->name('files.edit');
    Route::patch('/files/{file}', [FileController::class, 'update'])->name('files.update');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');

    // ゴミ箱からの復元、および物理削除
    Route::post('/files/{id}/restore', [FileController::class, 'restore'])->name('files.restore');
    Route::delete('/files/{id}/force', [FileController::class, 'forceDelete'])->name('files.force-delete');

    // 共有リンクの発行（トークン生成）
    Route::post('/files/{file}/share', [FileController::class, 'createShareLink'])->name('files.share');
    
    
    // --- Breeze: プロフィール管理 ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';