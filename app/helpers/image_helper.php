<?php

function kompres_gambar($source, $target, $maxWidth = 1600, $quality = 78)
{
    if (!extension_loaded('gd')) {

        return @copy($source, $target);
    }

    $info = @getimagesize($source);
    if ($info === false) return @copy($source, $target);

    [$width, $height] = $info;
    $type = $info[2];


    switch ($type) {
        case IMAGETYPE_JPEG:
            $src = @imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $src = @imagecreatefrompng($source);
            break;
        case IMAGETYPE_WEBP:
            $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : null;
            break;
        default:
            return @copy($source, $target);
    }
    if (!$src) return @copy($source, $target);


    if ($width > $maxWidth) {
        $newWidth  = $maxWidth;
        $newHeight = (int) round($height * ($maxWidth / $width));
    } else {
        $newWidth  = $width;
        $newHeight = $height;
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);


    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);


    $folder = dirname($target);
    if (!is_dir($folder)) @mkdir($folder, 0755, true);


    $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    $ok = false;
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $ok = imagejpeg($dst, $target, $quality);
            break;
        case 'png':

            $pngQuality = (int) round((100 - $quality) / 11);
            $ok = imagepng($dst, $target, max(0, min(9, $pngQuality)));
            break;
        case 'webp':
            $ok = function_exists('imagewebp') ? imagewebp($dst, $target, $quality) : imagejpeg($dst, $target, $quality);
            break;
        default:
            $ok = imagejpeg($dst, $target, $quality);
    }

    imagedestroy($src);
    imagedestroy($dst);
    return $ok;
}

/**
 * Helper: pindah file upload sambil dikompresi.
 * Aman dipakai sebagai pengganti move_uploaded_file().
 */
function pindah_dan_kompres_upload($tmpName, $target, $maxWidth = 1600, $quality = 78)
{
    if (!is_uploaded_file($tmpName)) return false;
    $ok = kompres_gambar($tmpName, $target, $maxWidth, $quality);
    if ($ok) @unlink($tmpName);
    return $ok;
}
