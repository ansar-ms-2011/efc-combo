<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} API</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind via CDN (no install needed) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-xl p-10 text-center w-full max-w-lg">

        <h1 class="text-3xl font-bold mb-2">
            🚀 {{ config('app.name') }} API
        </h1>

        <p class="text-gray-500 mb-6">
            Backend API is running successfully.
        </p>

        <div class="grid grid-cols-2 gap-4">

            <a href="#"
               class="bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                API Base
            </a>

            <a href="#"
               class="bg-gray-800 text-white py-3 rounded-lg hover:bg-black">
                Login
            </a>

            <a href="#"
               class="bg-green-600 text-white py-3 rounded-lg hover:bg-green-700">
                Health Check
            </a>

            <a href="#"
               class="bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700">
                Test Auth
            </a>

        </div>

        <div class="mt-8 text-sm text-gray-400">
            Laravel {{ app()->version() }} ({{ app()->environment() }})
        </div>
    </div>
</div>

</body>
</html>
