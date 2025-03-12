<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mostrar PDF</title>
</head>
<body>
    <h1>Mostrando PDF: {{ $document->file_name }}</h1>
    <iframe
        src="{{ asset('storage/' . $document->file_path) }}"
        width="100%"
        height="600"
    >
    </iframe>
</body>
</html>
