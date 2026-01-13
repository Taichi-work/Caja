<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Caja - ゴミ箱') }}
            </h2>
            <div class="flex items-center">
                <a href="{{ route('files.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                    {{ __('← ファイル一覧に戻る') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-4 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-red-500">削除済みのファイル</h3>
                    
                    @if($files->isEmpty())
                        <p class="text-gray-500 text-center py-10">ゴミ箱は空です。</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-left">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-sm font-medium text-gray-500 uppercase">ファイル名</th>
                                        <th class="px-6 py-3 text-sm font-medium text-gray-500 uppercase">アップロード者</th>
                                        <th class="px-6 py-3 text-sm font-medium text-gray-500 uppercase">サイズ</th>
                                        <th class="px-6 py-3 text-sm font-medium text-gray-500 uppercase">削除日</th>
                                        <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase">アクション</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($files as $file)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-medium italic">
                                                {{ $file->original_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                {{ $file->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                {{ number_format($file->size / 1024, 2) }} KB
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                {{ $file->deleted_at->format('Y/m/d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end items-center space-x-4 whitespace-nowrap">
                                                    
                                                    <form action="{{ route('files.restore', $file->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-bold">復元</button>
                                                    </form>
                                                    
                                                    <form action="{{ route('files.force-delete', $file->id) }}" method="POST" onsubmit="return confirm('警告：この操作は取り消せません。ストレージからも完全に削除されます。よろしいですか？');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">完全に削除</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>