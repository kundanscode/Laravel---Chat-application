<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-900 text-white flex items-center justify-center h-screen">

    <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full border border-gray-700">
        <h1
            class="text-3xl font-bold mb-6 text-center bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-500">
            Join Secret Chat
        </h1>

        <form action="{{ route('chat.login.post', $secretKey) }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Display Name</label>
                <input type="text" id="name" name="name" required autofocus
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-white placeholder-gray-500 transition duration-200"
                    placeholder="Enter your name...">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-md transition duration-200 transform hover:scale-[1.02]">
                Join Chat
            </button>
        </form>

        <p class="mt-4 text-xs text-center text-gray-500">
            Messages are ephemeral and not saved.
        </p>
    </div>

</body>

</html>
