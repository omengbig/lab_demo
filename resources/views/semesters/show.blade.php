<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">Semesters</a> / {{ $semester->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md shadow-sm mb-4">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Create Group Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-medium mb-1">Create a New Group</h3>
                        <p class="text-sm text-gray-500">Add a new class section or group to this semester.</p>
                    </div>
                    <form action="{{ route('groups.store', $semester) }}" method="POST" class="flex gap-2">
                        @csrf
                        <x-text-input name="name" type="text" placeholder="e.g. Group A - SWE301" required />
                        <x-primary-button>Create Group</x-primary-button>
                    </form>
                </div>
            </div>

            <!-- Groups List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Groups in {{ $semester->name }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($semester->groups as $group)
                            <a href="{{ route('groups.show', $group) }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition flex justify-between items-center">
                                <div>
                                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">{{ $group->name }}</h5>
                                    <p class="text-sm text-gray-600">{{ $group->students_count }} Students</p>
                                </div>
                                <div>
                                    <span class="text-blue-600 font-medium">View Hub &rarr;</span>
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">No groups exist in this semester yet. Create one above.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
