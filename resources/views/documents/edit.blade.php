<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Documento</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl mb-4">Editar Documento</h1>

        {{-- Mostrar errores de validación --}}
        @if ($errors->any())
            <div class="text-red-500 mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="mt-1">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('documents.update', $document->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="file_name" class="block font-medium mb-1">Nombre del Documento</label>
                <input
                    type="text"
                    name="file_name"
                    id="file_name"
                    class="border border-gray-300 rounded px-3 py-2 w-full"
                    value="{{ old('file_name', $document->file_name) }}"
                >
            </div>

            <div>
                <label for="archivo" class="block font-medium mb-1">
                    Reemplazar archivo (opcional)
                </label>
                <input
                    type="file"
                    name="archivo"
                    id="archivo"
                    class="border border-gray-300 rounded px-3 py-2 w-full"
                    accept=".jpg,.png,.pdf,.doc,.docx"
                >
            </div>

            <div class="flex space-x-2">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Guardar cambios
                </button>
                <a
                    href="{{ route('documents.index') }}"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>
