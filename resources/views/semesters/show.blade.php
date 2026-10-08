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
                            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition flex justify-between items-center">
                                <a href="{{ route('groups.show', $group) }}" class="block flex-1">
                                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 hover:text-blue-600">{{ $group->name }}</h5>
                                    <p class="text-sm text-gray-600">{{ $group->students_count }} Students</p>
                                </a>
                                <div class="flex items-center space-x-2 ml-4">
                                    <button onclick="let newName = prompt('Enter new group name:', '{{ $group->name }}'); if(newName && newName !== '{{ $group->name }}') { document.getElementById('edit-group-{{ $group->id }}-name').value = newName; document.getElementById('edit-group-{{ $group->id }}').submit(); }" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded border border-gray-300 transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group? All attendance records will be lost.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded border border-red-300 transition">
                                            Delete
                                        </button>
                                    </form>
                                    
                                    <form id="edit-group-{{ $group->id }}" action="{{ route('groups.update', $group) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" id="edit-group-{{ $group->id }}-name" value="">
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No groups exist in this semester yet. Create one above.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
