<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReaderException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtenemos todos los documentos de la tabla
        $documents = Document::all();

        // Retornamos la vista "documents.index" con la lista
        return view('documents.index', compact('documents'));
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

        // Solo validamos PDF si el mimeType es PDF
        if ($file->getClientMimeType() === 'application/pdf') {
            // 2. Guardar temporalmente el PDF sin persistir en la BD
            //    O podrías usar $file->getPathname() directamente
            $tmpPath = $file->getPathname();

            // 3. Intentar abrir el PDF con FPDI (versión gratuita)
            //    para ver si nos lanza la excepción de compresión
            try {
                $pdf = new Fpdi();
                // Si internamente falla con compresión avanzada,
                // saltará CrossReferenceException
                $pageCount = $pdf->setSourceFile($tmpPath);
            } catch (CrossReferenceException $e) {
                if ($e->getCode() === CrossReferenceException::COMPRESSED_XREF) {
                    // Este es el caso de “compresión no soportada”
                    // Si hubo error de compresión:
                    return response()->json([
                        'message' => 'Este PDF usa compresión avanzada no soportada..',
                    ], 422);
                }
                // Si es otro error, lo manejamos genéricamente
                return back()->withErrors(['archivo' => $e->getMessage()])->withInput();
            } catch (PdfReaderException $e) {
                // Otras excepciones de lectura
                return back()->withErrors(['archivo' => $e->getMessage()])->withInput();
            } catch (\Exception $e) {
                // Cualquier otro error genérico
                return back()->withErrors(['archivo' => 'Error al procesar el PDF: ' . $e->getMessage()])->withInput();
            }
        }

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
    public function show($id)
    {
        $document = Document::findOrFail($id);
        // Retornamos una vista, p.ej. "documents.show",
        // enviándole el $document para poder ver su ruta
        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // 1. Obtener el documento de la BD
        $document = Document::findOrFail($id);

        // 2. Retornar la vista de edición
        return view('documents.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. Obtener el documento
        $document = Document::findOrFail($id);

        // 2. Validar si se subió archivo nuevo o solo se actualiza nombre
        $request->validate([
            'file_name' => 'required|string|max:255',
            // El archivo no siempre es requerido; solo si deseas permitir reemplazar el PDF/Doc
            'archivo'   => 'nullable|file|mimes:jpg,png,pdf,doc,docx|max:512000000',
        ]);

        // 3. Actualizar nombre en la BD
        $document->file_name = $request->input('file_name');

        // 4. Si se subió un nuevo archivo, reemplazar
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');

            // Generar nombre nuevo
            $originalName = time() . '_' . $file->getClientOriginalName();
            $fileType     = $file->getClientMimeType();
            $fileSize     = $file->getSize();

            // Guardar físicamente el archivo
            $path = $file->storeAs('uploads', $originalName, 'public');

            // Actualizar datos en la base
            $document->file_path = $path;
            $document->file_type = $fileType;
            $document->file_size = $fileSize;
        }

        $document->save();

        // 5. Redireccionar a la lista o a donde quieras
        return redirect()->route('documents.index')
            ->with('success', 'Documento actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function downloadMerged()
    {
        // 1. Obtener solo documentos que sean PDFs
        //    (file_type = 'application/pdf')
        $documents = Document::where('file_type', 'application/pdf')->get();

        // 2. Inicializar la librería de fusión
        $oMerger = PDFMerger::init();

        // 3. Agregar cada PDF al merger
        foreach ($documents as $doc) {
            $filePath = storage_path('app/public/' . $doc->file_path);
            // 'all' indica que tome todas las páginas
            $oMerger->addPDF($filePath, 'all');
        }

        // 4. Realizar la fusión
        //    "merge()" no descarga automáticamente, solo genera el buffer de datos
        $oMerger->setFileName('all-documents-merged.pdf');
        $oMerger->merge();
        return $oMerger->download();
    }
}
