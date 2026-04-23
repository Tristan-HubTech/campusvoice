<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Stores optional comment images under FCPATH/uploads/social_comment/{Y/m}/random.ext
 */
class CommentImageStorage
{
    public const MAX_BYTES = 5_242_880; // 5 MB

    public const SUBDIR = 'uploads' . DIRECTORY_SEPARATOR . 'social_comment';

    private const ALLOWED_EXT = ['jpg', 'png', 'webp'];

    public static function baseDir(): string
    {
        return rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . self::SUBDIR;
    }

    public static function publicUrl(?string $relativePath): string
    {
        if ($relativePath === null || $relativePath === '') {
            return '';
        }

        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
        $relativePath = ltrim($relativePath, '/');
        if (str_starts_with($relativePath, 'uploads/social_comment/')) {
            $relativePath = substr($relativePath, strlen('uploads/social_comment/'));
        }

        return base_url('uploads/social_comment/' . $relativePath);
    }

    public static function tryStore(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        if (! $file->isValid()) {
            if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                return null;
            }
            throw new \RuntimeException($file->getErrorString() ?? 'Upload failed.');
        }

        if ($file->getSize() > self::MAX_BYTES) {
            throw new \RuntimeException('Image must be 5 MB or smaller.');
        }

        $ext = strtolower((string) $file->getClientExtension());
        if (in_array($ext, ['jpg', 'jpeg'], true)) {
            $ext = 'jpg';
        }
        if (! in_array($ext, self::ALLOWED_EXT, true)) {
            throw new \RuntimeException('Use JPG, PNG, or WebP only.');
        }

        $sub = date('Y/m');
        $dir = self::baseDir() . DIRECTORY_SEPARATOR . $sub;
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new \RuntimeException('Could not create upload directory.');
        }

        $newName = bin2hex(random_bytes(16)) . '.' . $ext;
        if ($file->move($dir, $newName, true) === false) {
            throw new \RuntimeException('Could not save image.');
        }

        return $sub . '/' . $newName;
    }

    public static function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $relativePath = str_replace(['../', '..\\', '\\'], '', $relativePath);
        $full         = self::baseDir() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
