<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ファイル共有 - Caja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-indigo-600 italic">Caja</h1>
            <p class="text-gray-500">Secure File Sharing</p>
        </div>

        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl overflow-hidden p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-50 text-indigo-500 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800 break-words">{{ $file->original_name }}</h2>
                <p class="text-sm text-gray-400 mt-1">{{ number_format($file->size / 1024 / 1024, 2) }} MB</p>
            </div>

            <hr class="my-6 border-gray-100">

            <div class="space-y-4">
                <p class="text-gray-600 text-sm">
                    このファイルは、Cajaユーザーからあなたに共有されました。<br>
                    有効期限内であれば、以下のボタンからダウンロードできます。
                </p>
                
                <a href="{{ route('files.shared.download', $file->share_token) }}" 
                   class="block w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition duration-200">
                    ファイルをダウンロード
                </a>

                <p class="text-xs text-gray-400">
                    有効期限: {{ $file->shared_expires_at->format('Y年m月d日 H:i') }} まで
                </p>
            </div>
        </div>

        <div class="mt-8 text-sm text-gray-400">
            &copy; {{ date('Y') }} Caja - Simple & Secure.
        </div>
    </div>
</body>
</html>