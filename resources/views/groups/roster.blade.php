<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('semesters.show', $group->semester) }}" class="text-blue-500 hover:underline">{{ $group->semester->name }}</a> / 
            <a href="{{ route('groups.show', $group) }}" class="text-blue-500 hover:underline">{{ $group->name }}</a> / 
            Manage Master Roster
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Import Name List</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Register students to this group. You must complete this step before uploading attendance PDFs. The system will look for a 6-digit matrix number followed by a name. You can upload a file (PDF, DOCX, XLSX, CSV, TXT) or simply paste the raw text below.
                    </p>

                    @if(session('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <p class="text-red-700">{{ session('error') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('groups.roster.store', $group) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">1. Upload Document (Optional)</label>
                            <input type="file" name="roster_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept=".pdf,.csv,.txt,.xlsx,.docx">
                            <p class="mt-1 text-xs text-gray-400">Supported: PDF, DOCX, XLSX, CSV, TXT</p>
                        </div>

                        <div class="mb-4 mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">2. Paste Raw Text (Optional)</label>
                            <textarea name="manual_text" rows="8" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="103793 JOHN DOE&#10;103794 JANE SMITH"></textarea>
                        </div>

                        <div class="flex justify-end mt-6">
                            <x-primary-button>
                                Process Roster
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
