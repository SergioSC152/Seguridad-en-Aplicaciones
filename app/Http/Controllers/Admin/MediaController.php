<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Muestra el listado de archivos multimedia (Biblioteca Multimedia).
     */
    public function index()
    {
        $media = Media::latest()->paginate(12);
        return view('admin.media.index', compact('media'));
    }

    /**
     * Almacena una nueva imagen en el disco público y registra los metadatos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120', // Límite de 5 MB
            ],
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        Media::create([
            'name' => $validated['name'],
            'path' => $path,
            'url' => '/storage/'.$path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'active' => true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Archivo cargado correctamente.');
    }

    /**
     * Actualiza o reemplaza una imagen gestionando el archivo anterior.
     */
    public function update(Request $request, Media $media)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120',
            ],
        ]);

        $media->name = $validated['name'];

        if ($request->hasFile('file')) {
            // Eliminar archivo físico anterior para evitar residuos
            if ($media->path && Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }

            $file = $request->file('file');
            $path = $file->store('media', 'public');

            $media->path = $path;
            $media->url = '/storage/'.$path;
            $media->mime_type = $file->getMimeType();
            $media->size = $file->getSize();
        }

        $media->save();

        return redirect()
            ->back()
            ->with('success', 'Archivo multimedia actualizado y reemplazado con éxito.');
    }

    /**
     * Elimina el registro y el archivo físico del disco público.
     */
    public function destroy(Media $media)
    {
        if ($media->path && Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return redirect()
            ->back()
            ->with('success', 'Archivo multimedia eliminado correctamente.');
    }
}
