<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard - Semesters') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md shadow-sm mb-4">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Create Semester Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-medium mb-1">Create a New Semester</h3>
                        <p class="text-sm text-gray-500">Add a new academic semester to start organizing groups.</p>
                    </div>
                    <form action="{{ route('semesters.store') }}" method="POST" class="flex gap-2">
                        @csrf
                        <x-text-input name="name" type="text" placeholder="e.g. Fall 2024" required />
                        <x-primary-button>Create Semester</x-primary-button>
                    </form>
                </div>
            </div>

            <!-- Semesters List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Total Semesters Created: {{ collect($semesters)->count() }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse($semesters as $semester)
                            <a href="{{ route('semesters.show', $semester) }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
                                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">{{ $semester->name }}</h5>
                                <p class="text-sm text-gray-600">{{ $semester->groups_count }} Groups</p>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">No semesters exist yet. Create one above.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
