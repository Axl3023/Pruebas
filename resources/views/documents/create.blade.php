<!DOCTYPE html>
<html>
<head>
    <title>Subir Documento</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.jsx'])
</head>
<body>
    <h1>Subir Documento</h1>

    {{-- @if(session('success'))
        <div style="color: green;">
            {{ session('success') }}
            <br>
            ID del documento creado: {{ session('document_id') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="archivo">Selecciona un archivo:</label>
        <input type="file" name="archivo" id="archivo"><br><br>
        <button type="submit">Subir</button>
    </form> --}}
    <div id="app"></div>
</body>
</html>
