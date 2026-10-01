<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CowAppLogoSeeder extends Seeder
{
    public function run(): void
    {
        $source = resource_path('branding/cowapp-logo.png');
        $path = 'branding/cowapp-logo.png';

        if (! is_file($source) || filesize($source) > 5 * 1024 * 1024) {
            throw new RuntimeException('El logo no existe o supera el límite de 5 MB.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($source);

        if ($mime !== 'image/png' || getimagesize($source) === false) {
            throw new RuntimeException('El logo debe ser una imagen PNG válida.');
        }

        if (! Storage::disk('public')->put($path, file_get_contents($source))) {
            throw new RuntimeException('No se pudo guardar el logo en el disco público.');
        }

        Media::query()->updateOrCreate(
            ['name' => Media::COWAPP_LOGO_NAME],
            [
                'path' => $path,
                'url' => '/storage/'.$path,
                'mime_type' => $mime,
                'size' => filesize($source),
                'active' => true,
            ]
        );
    }
}
