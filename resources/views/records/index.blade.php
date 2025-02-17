<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

<div class="w-full max-w-5xl bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Records List</h1>

    <div class="text-right mb-4">
        <form action="{{ route('settings.edit') }}" method="GET">
            @csrf
            <button class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition">
                Add Sheet URL
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
            ❌ {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-2 justify-center mb-4">
        <form action="{{ route('records.generate') }}" method="POST">
            @csrf
            <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                Generate 1000 Records
            </button>
        </form>

        <form action="{{ route('records.sync') }}" method="POST">
            @csrf
            <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                Sync with Google Sheets
            </button>
        </form>

        <form action="{{ route('records.clear') }}" method="POST">
            @csrf
            <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                Clear Table
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse shadow-md rounded-lg overflow-hidden">
            <thead>
            <tr class="bg-gray-800 text-white">
                <th class="px-4 py-2 text-center">ID</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2 text-center">Status</th>
                <th class="px-4 py-2 text-center"> Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($records as $record)
                <tr class="border-b hover:bg-gray-100">
                    <td class="px-4 py-2 text-center">{{ $record->id }}</td>
                    <td class="px-4 py-2">{{ $record->name }}</td>
                    <td class="px-4 py-2 text-center">
                        <form action="{{ route('records.update', $record->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" onchange="this.form.submit()"
                                    class="border rounded px-2 py-1 bg-white text-gray-800">
                                <option value="Allowed" {{ $record->status === 'Allowed' ? 'selected' : '' }}>✅
                                    Allowed
                                </option>
                                <option value="Prohibited" {{ $record->status === 'Prohibited' ? 'selected' : '' }}>🚫
                                    Prohibited
                                </option>
                            </select>
                        </form>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <form action="{{ route('records.destroy', $record->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $records->links('pagination::tailwind') }}
    </div>
</div>

</body>
</html>
