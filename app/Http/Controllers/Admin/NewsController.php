<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Muestra las noticias en el panel administrativo.
     */
    public function index()
    {
        $news = News::with('media')->latest()->paginate(10);
        $mediaList = Media::latest()->get();
        return view('admin.news.index', compact('news', 'mediaList'));
    }

    /**
     * Guarda una nueva publicación de noticia asociando multimedia.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:20000'],
            'media_id' => ['nullable', 'exists:media,id'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'published' => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(5);
        $mediaId = $validated['media_id'] ?? null;

        // Si se subió un archivo directamente en la publicación
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('media', 'public');
            $media = Media::create([
                'name' => $validated['title'],
                'path' => $path,
                'url' => '/storage/'.$path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
            $mediaId = $media->id;
        }

        News::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'media_id' => $mediaId,
            'published' => $request->boolean('published', true),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Publicación creada exitosamente.');
    }

    /**
     * Actualiza una noticia y gestiona el reemplazo de su imagen (Paso 5.13).
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:20000'],
            'media_id' => ['nullable', 'exists:media,id'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'published' => ['nullable', 'boolean'],
        ]);

        $news->title = $validated['title'];
        $news->excerpt = $validated['excerpt'] ?? null;
        $news->content = $validated['content'];
        $news->published = $request->boolean('published', false);

        if (!empty($validated['media_id'])) {
            $news->media_id = $validated['media_id'];
        }

        // Lógica de reemplazo de imagen según el paso 5.13 de la guía
        if ($request->hasFile('file')) {
            if ($news->media) {
                Storage::disk('public')->delete($news->media->path);
                $path = $request->file('file')->store('media', 'public');
                $news->media->update([
                    'path' => $path,
                    'url' => '/storage/'.$path,
                    'mime_type' => $request->file('file')->getMimeType(),
                    'size' => $request->file('file')->getSize(),
                ]);
            } else {
                $file = $request->file('file');
                $path = $file->store('media', 'public');
                $media = Media::create([
                    'name' => $news->title,
                    'path' => $path,
                    'url' => '/storage/'.$path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
                $news->media_id = $media->id;
            }
        }

        $news->save();

        return redirect()
            ->back()
            ->with('success', 'Publicación actualizada correctamente.');
    }

    /**
     * Elimina una noticia.
     */
    public function destroy(News $news)
    {
        $news->delete();

        return redirect()
            ->back()
            ->with('success', 'Publicación eliminada correctamente.');
    }
}
