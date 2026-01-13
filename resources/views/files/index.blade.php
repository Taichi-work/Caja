<x-app-layout>
    <x-slot name="header">
        {{-- タイトルとゴミ箱ボタンを両端に配置 --}}
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Caja - マイファイル') }}
            </h2>
            <div class="flex items-center">
                <a href="{{ route('files.trash') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('ゴミ箱を見る') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- アップロードフォームとエラーメッセージ --}}
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-4">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">新しいファイルをアップロード</label>
                            <input type="file" name="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-bold transition">
                            アップロード
                        </button>
                    </form>

                    {{-- バリデーションエラーの日本語表示 --}}
                    @if ($errors->any())
                        <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- ファイル一覧テーブル --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ファイル名 / 日付</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">アップロード者</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">サイズ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">アクション</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($files as $file)
                                <tr class="hover:bg-gray-50 transition">
                                    {{-- ファイル名（省略表示）と日付 --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-900 font-medium truncate max-w-[400px]" title="{{ $file->original_name }}">
                                                {{ $file->original_name }}
                                            </span>
                                            <span class="text-[11px] text-gray-400 mt-0.5">
                                                {{ $file->created_at->format('Y/m/d H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- アップロード者（折り返し防止） --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="inline-block px-2 py-0.5 bg-gray-100 rounded text-xs">
                                            {{ $file->user->name }}
                                        </span>
                                    </td>

                                    {{-- サイズ --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($file->size / 1024, 2) }} KB
                                    </td>

                                    {{-- アクション列 --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center space-x-4">
                                            
                                            {{-- 共有リンク発行セクション --}}
                                            <div class="flex flex-col items-end">
                                                @if($file->share_token && !$file->shared_expires_at->isPast())
                                                    {{-- 有効なリンクがある場合 --}}
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-[10px] text-green-600 bg-green-50 px-2 py-0.5 rounded-full font-bold uppercase">Shared</span>
                                                        <input type="text" readonly value="{{ route('files.shared', $file->share_token) }}" 
                                                            class="text-xs p-1 border rounded bg-gray-50 w-32 focus:ring-0"
                                                            onclick="this.select()">
                                                    </div>
                                                    <p class="text-[10px] text-gray-400 mt-1">期限: {{ $file->shared_expires_at->format('m/d H:i') }}</p>
                                                @else
                                                    {{-- 未発行または期限切れの場合 --}}
                                                    <form action="{{ route('files.share', $file) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-blue-600 hover:text-blue-900 flex items-center text-xs">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 100-2.684 3 3 0 000 2.684zm0 9.316a3 3 0 100-2.684 3 3 0 000 2.684z" />
                                                            </svg>
                                                            {{ $file->share_token ? '再発行' : 'リンク発行' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>

                                            {{-- 操作ボタン --}}
                                            <div class="flex items-center space-x-3 border-l pl-4 border-gray-100">
                                                <a href="{{ route('files.download', $file) }}" class="text-indigo-600 hover:text-indigo-900" title="ダウンロード">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('files.edit', $file) }}" class="text-green-500 hover:text-green-700" title="ファイル名編集">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('files.destroy', $file) }}" method="POST" onsubmit="return confirm('ゴミ箱に移動しますか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-600" title="削除">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>