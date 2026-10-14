<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;

/**
 * Validation and storage policy for user-supplied images that are served from public/.
 *
 * Two independent layers, so neither alone has to be perfect:
 *  - rule(): raster formats only, up to 2 MB. Laravel's `image` rule is deliberately not
 *    used because it accepts SVG, which can carry script and would be served from our
 *    own origin. The underlying `mimes` rule also rejects any *.php / *.phtml / *.phar
 *    client filename, even when the content sniffs as an image (GIF/PHP polyglots).
 *  - store(): the client filename is never used. hashName() is 40 random characters
 *    plus an extension guessed from the file's content, so a stored file can never be
 *    executable and uploads cannot overwrite each other.
 */
final class ImageUpload
{
    public const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public const MAX_KILOBYTES = 2048;

    public static function rule(): File
    {
        return File::types(self::EXTENSIONS)->max(self::MAX_KILOBYTES);
    }

    /**
     * Store the file on the given disk and return its generated filename.
     */
    public static function store(UploadedFile $file, string $disk): string
    {
        return $file->store('', $disk);
    }
}
