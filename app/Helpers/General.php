<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


if (!function_exists('uploadDocument')) {
    function uploadDocument(Request $request, $folder, $name_file_on_request, $disk)
    {

        $fileNameOriginal = $request->file($name_file_on_request)->getClientOriginalName();
        $extension = Str::slug(pathinfo($fileNameOriginal, PATHINFO_FILENAME)) . '.' . pathinfo($fileNameOriginal, PATHINFO_EXTENSION);
        $file_name = Str::random(32) . '_' . $extension;

        $path = $request->file($name_file_on_request)->storeAs($folder, $file_name, $disk);

        return $path;
    }

}

if (!function_exists('uploadFile')) {
    function uploadFile($file, $folder, $disk = null, $oldPath = null)
    {
        $disk = $disk ?? config('filesystems.default');
        if ($oldPath) {
            deleteFile($oldPath, $disk);
        }
        $fileNameOriginal = $file->getClientOriginalName();
        $finalName = Str::slug(pathinfo($fileNameOriginal, PATHINFO_FILENAME)) . '.' . pathinfo($fileNameOriginal, PATHINFO_EXTENSION);;
        $file_name = time() . Str::random(15) . '-' . $finalName;
        if ($disk == 's3') {
            $folder = 'flying-arrow/' . $folder;
        }
        return $file->storeAs($folder, $file_name, $disk);

    }

}



if (!function_exists('handleUploadedFiles')) {
    function handleUploadedFiles(Request $request, mixed $data, array $names, $disk = null, ?Model $updatedRow = null): mixed
    {
        $disk = $disk ?? config('filesystems.default');
        foreach ($names as $name) {
            if ($request->hasFile($name) && $request->file($name)->isValid()) {
                $file = $request->file($name);
                if (!isset($updatedRow)) {
                    $data[$name] = uploadFile($file, "users/$name", $disk);
                } else {
                    $data[$name] = uploadFile($file, "users/$name", $disk, $updatedRow->{$name});
                }
            }
        }
        return $data;
    }
}

if (!function_exists('FileUrl')) {
    function FileUrl(?string $image, $disk = null)
    {
        if (!$image) {
            return null;
        }

        if (Str::startsWith($image, ['http', 'https'])) {
            return $image;
        }

        $disk = $disk ?? config('filesystems.default');

        if ($disk == 's3') {
            return Storage::disk($disk)->temporaryUrl($image, now()->addDays(6));
        }
        return Storage::disk($disk)->url($image);
    }
}


if (!function_exists('deleteFile')) {
    function deleteFile($path, $disk = 'public')
    {
        if (isset($path)) {
            $path = str_replace(asset('storage') . '/', '', $path);
            Storage::disk($disk)->delete($path);
        }
    }
}





