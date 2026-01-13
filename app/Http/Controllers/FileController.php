<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * ファイル一覧を表示 (通常)
     */
    public function index()
    {
        $files = File::with('user')
                     ->where('user_id', Auth::id())
                     ->latest()
                     ->get();

        return view('files.index', compact('files'));
    }

    /**
     * ファイルを保存
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ], [
            'file.required' => 'ファイルを選択してください。',
            'file.file'     => '正しいファイルをアップロードしてください。',
            'file.max'      => '20MBを超えています。',
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('uploads', 'public');

            File::create([
                'user_id'       => Auth::id(),
                'original_name' => $uploadedFile->getClientOriginalName(),
                'storage_path'  => $path,
                'size'          => $uploadedFile->getSize(),
                'mime_type'     => $uploadedFile->getMimeType(),
            ]);
        }

        return redirect()->route('files.index')->with('status', 'ファイルをアップロードしました！');
    }

    /**
     * ダウンロード処理
     */
    public function download(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        $path = storage_path('app/public/' . $file->storage_path);
        return response()->download($path, $file->original_name);
    }

    /**
     * 編集画面の表示
     */
    public function edit(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }
        return view('files.edit', compact('file'));
    }

    /**
     * 名前を更新
     */
    public function update(Request $request, File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'original_name' => 'required|string|max:255',
        ]);

        $file->update([
            'original_name' => $request->original_name,
        ]);

        return redirect()->route('files.index')->with('status', 'ファイル名を更新しました。');
    }

    /**
     * ゴミ箱に移動 (Soft Delete)
     */
    public function destroy(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        // ここで delete() を呼ぶと、Model側の SoftDeletes トレイトにより
        // 自動的に deleted_at カラムに日時が入り、論理削除になります。
        $file->delete();

        return redirect()->route('files.index')->with('status', 'ファイルをゴミ箱に移動しました。');
    }

    /**
     * ゴミ箱一覧を表示
     */
    public function trash()
    {
        // 論理削除されたデータのみを取得
        $files = File::onlyTrashed()
                     ->where('user_id', Auth::id())
                     ->latest()
                     ->get();

        return view('files.trash', compact('files'));
    }

    /**
     * ファイルを復元 (Restore)
     */
    public function restore($id)
    {
        $file = File::onlyTrashed()->findOrFail($id);

        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        $file->restore();

        return redirect()->route('files.trash')->with('status', 'ファイルを復元しました。');
    }

    /**
     * 完全に削除 (物理削除)
     */
    public function forceDelete($id)
    {
        $file = File::onlyTrashed()->findOrFail($id);

        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        // 物理ファイルを削除
        if (Storage::disk('public')->exists($file->storage_path)) {
            Storage::disk('public')->delete($file->storage_path);
        }

        // DBレコードを物理削除
        $file->forceDelete();

        return redirect()->route('files.trash')->with('status', 'ファイルを完全に削除しました。');
    }

    /**
     * 共有リンクを作成・発行する
     */
    public function createShareLink(File $file)
    {
        // 自分のファイルかチェック
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        // ランダムな32文字のトークンを生成し、24時間後を期限にする
        $file->update([
            'share_token' => Str::random(32),
            'shared_expires_at' => now()->addMinute(1),
        ]);

        return redirect()->back()->with('status', '共有リンクを作成しました！');
    }

    /**
     * 共有ページを表示（ログイン不要）
     */
    public function showShared($token)
    {
        // トークンが一致し、かつ期限が切れていないものを探す
        $file = File::where('share_token', $token)
                    ->where('shared_expires_at', '>', now())
                    ->firstOrFail();

        return view('files.shared', compact('file'));
    }

    /**
     * 共有ページからのダウンロード（ログイン不要）
     */
    public function sharedDownload($token)
    {
        $file = File::where('share_token', $token)
                    ->where('shared_expires_at', '>', now())
                    ->firstOrFail();

        $path = storage_path('app/public/' . $file->storage_path);
        return response()->download($path, $file->original_name);
    }
}