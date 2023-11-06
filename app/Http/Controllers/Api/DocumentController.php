<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = DocumentResource::collection(Document::all());
        return response()->json($documents, 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:40',
                'description' => 'required|string|max:100',
                'file' => 'required|file|mimes:pdf,docx|max:10280',
                'date_document' => 'required|date',
                'status' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()]);
            }

            $document = $request->all();

            if ($file = $request->file('file')) {
                $path = 'assets/documents/';
                $fileName = $file->getClientOriginalName();
                $file->move($path, $fileName);
                $document['file'] = $fileName;
            }

            Document::create($document);

            return response()->json([
                'status' => true,
                'document' => $document,
                'message' => 'Operación realizada con éxito!'
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Document $document)
    {
        return response()->json($document, 200);
    }

    public function update(Request $request, Document $document)
    {
        try {

			$validator = Validator::make($request->all(), [
                'title' => 'required|string|max:40',
                'description' => 'required|string|max:100',
                'date_document' => 'required|date',
                'status' => 'required|string',
            ]);

			if ($request->hasFile('file')){
				$validator->addRules([
					'file' => 'file|mimes:pdf,docx|max:10280',
				]);
			}

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()]);
            }

            $documentUpdate = $request->all();

            if ($file = $request->file('file')) {
                // Eliminar el documento si existe
                if ($document->file) {
                    $path = public_path('assets/documents/' . $document->file);

                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                // Guardar el nuevo documento
                $routeSave = 'assets/documents/';
                $fileName = $file->getClientOriginalName(); // Obtener el nombre original del documento
                $file->move($routeSave, $fileName);
                $documentUpdate['file'] = $fileName;
            } else {
                // Mantener el documento anterior
                $documentUpdate['file'] = $document->file;
            }

            $document->update($documentUpdate);

            return response()->json([
                'status' => true,
                'document' => $document,
                'message' => 'Operación realizada con éxito!'
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Document $document)
    {
        try {
            if ($document->file) {
                $path = public_path('assets/documents/' . $document->file);

                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $document->delete();

            return response()->json([
                'status' => true,
                'message' => 'Operación realizada con éxito!'
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
