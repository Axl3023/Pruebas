<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lista de Documentos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite('resources/css/app.css') <!-- Asegúrate de compilar CSS con Tailwind -->

    <script>
        function openPdfModal(pdfUrl) {
            // 1. Obtener el iframe del modal
            const iframe = document.getElementById('modalPdfIframe');
            iframe.src = pdfUrl;

            // 2. Mostrar el modal
            const modal = document.getElementById('pdfModal');
            modal.classList.remove('hidden'); // o .add('block')
        }

        function closePdfModal() {
            // Cerrar el modal
            const modal = document.getElementById('pdfModal');
            modal.classList.add('hidden'); // o .remove('block')

            // Limpiar el src del iframe (opcional)
            const iframe = document.getElementById('modalPdfIframe');
            iframe.src = "";
        }
    </script>
</head>

<body class="bg-gray-100 p-6">

    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">Documentos</h1>

        <div class="mb-4">
            <a href="{{ route('documents.create') }}"
                class="inline-block px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                Subir nuevo documento
            </a>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded">
            <table class="min-w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-3 px-6 font-medium text-gray-700 uppercase">ID</th>
                        <th class="py-3 px-6 font-medium text-gray-700 uppercase">Nombre</th>
                        <th class="py-3 px-6 font-medium text-gray-700 uppercase">Tipo</th>
                        <th class="py-3 px-6 font-medium text-gray-700 uppercase">Tamaño (bytes)</th>
                        <th class="py-3 px-6 font-medium text-gray-700 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($documents as $document)
                        <tr>
                            <td class="py-3 px-6">{{ $document->id }}</td>
                            <td class="py-3 px-6 flex items-center gap-3">
                                <i class="fa-solid fa-file-pdf fa-xl" style="color: #d71919;"></i>
                                <p>{{ $document->file_name }}</p>
                            </td>
                            <td class="py-3 px-6">{{ $document->file_type }}</td>
                            <td class="py-3 px-6">{{ $document->file_size }}</td>
                            <td class="py-3 px-6">
                                <button class="text-blue-600 hover:underline"
                                    onclick="openPdfModal('{{ asset('storage/' . $document->file_path) }}')">
                                    Ver
                                </button>
                                <!-- Podrías agregar más acciones: Editar, Eliminar, etc. -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="pdfModal" class="hidden fixed inset-0 bg-transparent bg-opacity-75 flex justify-center items-center">
                <div class="bg-white rounded p-4 w-11/12 h-5/6">
                    <button class=" flex top-2 right-2 text-red-500 font-bold justify-end" onclick="closePdfModal()">
                        Cerrar
                    </button>

                    <iframe id="modalPdfIframe" class="w-full h-full" src="">
                    </iframe>
                </div>
            </div>

            @if ($documents->isEmpty())
                <p class="p-4 text-gray-600">No hay documentos subidos aún.</p>
            @endif
        </div>
        <a href="{{ route('documents.downloadAll') }}"
            class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Descargar todos los PDFs unificados
        </a>
    </div>

</body>

</html>
