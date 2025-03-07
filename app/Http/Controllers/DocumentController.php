<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validar el archivo
        $request->validate([
            'archivo' => 'required|file|mimes:jpg,png,pdf,doc,docx|max:512000000',
        ], [
            'archivo.required' => 'Debes seleccionar un archivo',
            'archivo.file' => 'El archivo debe ser un archivo válido',
            'archivo.mimes' => 'Tipo de archivo no permitido',
            'archivo.max' => 'El archivo excede el tamaño máximo de 500 MB',
        ]);

        // 2. Obtener la instancia del archivo
        $file = $request->file('archivo');

        // 3. Obtener datos del archivo para guardar en la BD
        $originalName = time() . '_' . $file->getClientOriginalName();   // Nombre original Y Nombre unico de archivo
        $fileType = $file->getClientMimeType();           // Tipo MIME
        $fileSize = $file->getSize();                     // Tamaño en bytes

        // 4. Guardar el archivo en "storage/app/public/uploads"
        //    Se generará automáticamente un nombre único
        $path = $file->storeAs('uploads', $originalName, 'public');

        // 5. Crear el registro en la tabla `documents`
        $document = Document::create([
            'file_name' => $originalName,
            'file_path' => $path,  // por ejemplo: "uploads/xxxxx.jpg"
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ]);

        // 6. Redireccionar o retornar una vista
        // return redirect()->route('documents.create')
        //     ->with('success', 'Archivo subido y registrado con éxito.')
        //     ->with('document_id', $document->id);
        return response()->json([
            'document_id' => $document->id,
            'success' => true,
            'message' => 'Archivo subido correctamente'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
