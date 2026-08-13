<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Week Number -->
                        <div>
                            <x-input-label for="week_number" :value="__('Week Number')" />
                            <x-text-input id="week_number" name="week_number" type="number" class="mt-1 block w-full" required autofocus min="1" max="52" />
                            <x-input-error class="mt-2" :messages="$errors->get('week_number')" />
                        </div>

                        <!-- Session Date -->
                        <div>
                            <x-input-label for="session_date" :value="__('Session Date')" />
                            <x-text-input id="session_date" name="session_date" type="date" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('session_date')" />
                        </div>

                        <!-- PDF Upload -->
                        <div>
                            <x-input-label for="pdf_file" :value="__('Upload Attendance PDF')" />
                            <input id="pdf_file" name="pdf_file" type="file" accept=".pdf" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2" required>
                            <x-input-error class="mt-2" :messages="$errors->get('pdf_file')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Upload and Parse') }}</x-primary-button>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
