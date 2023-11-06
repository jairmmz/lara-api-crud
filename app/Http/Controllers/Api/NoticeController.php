<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Resources\NoticesResource;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = NoticesResource::collection(Notice::all());
        return response()->json($notices, 200);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:100',
                'slug' => 'required|string|max:255',
                'description' => 'required|string|max:100',
                'status' => 'required|string',
            ]);

			if ($request->hasFile('image')){
				$validator->addRules([
					'image' => 'image|mimes:jpg,jpeg,png,img,gif|max:10280',
				]);
			}

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()]);
            }

            $notice = $request->all();

            if ($image = $request->file('image')) {
                $path = 'assets/images/';
                $fileName = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($path, $fileName);
                $notice['image'] = "$fileName";
            }

            Notice::create($notice);

            return response()->json([
                'status' => 'success',
                'message' => 'Operación realizada con éxito!'
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el registro!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Notice $notice)
    {
        return response()->json($notice, 200);
    }

    public function update(Request $request, Notice $notice)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:100',
                'slug' => 'required|string|max:255',
                'description' => 'required|string|max:200',
                'status' => 'required|string',
            ]);

			if ($request->hasFile('image')){
				$validator->addRules([
					'image' => 'image|mimes:jpg,jpeg,png,img,gif|max:10280',
				]);
			}

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()]);
            }

            $noticeUpdate = $request->all();

            if ($image = $request->file('image')) {
                // Eliminar la imagen de perfil anterior si existe
                if ($notice->image) {
                    $path = public_path('assets/images/') . $notice->image;

                    if (file_exists($path)) {
                        unlink($path);
                    }
                }

                // Guardar la nueva imagen
                $routeSave = 'assets/images/';
                $imageName = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($routeSave, $imageName);
                $noticeUpdate['image'] = $imageName;
            } else {
                // Mantener la imagen de perfil anterior si no se seleccionó una nueva imagen
                unset($noticeUpdate['image']);
            }

            $notice->update($noticeUpdate);

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

    public function destroy(Notice $notice)
    {
        try {
            if ($notice->image) {
                $path = public_path('assets/images/') . $notice->image;

                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $notice->delete();

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
