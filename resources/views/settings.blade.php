<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки Google Sheets</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

<div class="w-full max-w-3xl bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">⚙️ Настройки Google Sheets</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('errors'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
            {!! session('errors') !!}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
        @csrf
        <label class="block text-gray-700 font-medium">🔗 Google Sheets URL:</label>
        <input type="text" name="google_sheet_url" value="{{ $settings->google_sheet_url ?? '' }}"
               placeholder="Введите URL Google Sheets"
               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>

        <div class="flex justify-between space-x-4">
            <a href="{{ route('records.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded-lg flex items-center space-x-2 hover:bg-gray-600 transition">
                ⬅️ <span>Назад</span>
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg flex items-center space-x-2 hover:bg-blue-600 transition">
                💾 <span>Сохранить</span>
            </button>
        </div>
    </form>
</div>

</body>
</html>
