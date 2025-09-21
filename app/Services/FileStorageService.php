<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    /**
     * Скачивает файл по URL и сохраняет в локальном хранилище.
     *
     * @param string|null $url   — исходная ссылка
     * @param string|null $name  — имя файла без расширения (если null → uniqid)
     * @param string $folder     — папка для сохранения (по умолчанию files)
     * @return string|null       — публичный URL или null, если не удалось сохранить
     */
    public function saveFromUrl(?string $url, ?string $name = null, string $folder = 'files'): ?string
    {
        if (!$url) {
            return null;
        }

        try {
            $response = Http::get($url);
            if (!$response->successful()) {
                return null;
            }

            // определяем расширение из ссылки
            $pathInfo  = pathinfo(parse_url($url, PHP_URL_PATH));
            $extension = $pathInfo['extension'] ?? null; // если неизвестно → null

            // имя файла
            $filename = $folder . '/' . ($name ?? uniqid('file_')) . ($extension ? '.' . $extension : '');

            Storage::disk('public')->put($filename, $response->body());

            return Storage::disk('public')->url($filename);
        } catch (\Throwable $e) {
            // можно добавить логирование ошибки
            return null;
        }
    }
}
