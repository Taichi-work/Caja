<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ファイルの編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('files.update', $file) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <x-input-label for="original_name" :value="__('ファイル名')" />
                        <x-text-input id="original_name" name="original_name" type="text" class="mt-1 block w-full" :value="old('original_name', $file->original_name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('original_name')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('更新') }}</x-primary-button>
                        <a href="{{ route('files.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('キャンセル') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>