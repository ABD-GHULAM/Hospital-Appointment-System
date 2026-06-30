<?php
/**
 * File upload helper
 */

function upload_profile_image(array $file): ?string
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
        return null;
    }

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0777, true);
        chmod(UPLOAD_PATH, 0777);
    }

    $extension = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => 'jpg',
    };

    $filename = uniqid('profile_', true) . '.' . $extension;
    $destination = UPLOAD_PATH . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        chmod($destination, 0644);
        return $filename;
    }

    return null;
}

function delete_profile_image(?string $filename): void
{
    if ($filename && file_exists(UPLOAD_PATH . $filename)) {
        unlink(UPLOAD_PATH . $filename);
    }
}
