<?php
namespace Sumo;
class ModelToolImage extends Model
{
    /**
    *
    *    @param filename string
    *    @param width
    *    @param height
    *    @param type char [default, w, h]
    *        default = scale with white space,
    *        w = fill according to width,
    *        h = fill according to height
    *
    */
    public function resize($filename, $width, $height, $type = "h")
    {
        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            return;
        }

        if (!$width) {
            $width = 1;
        }

        if (!$height) {
            $height = 1;
        }

        $info = pathinfo($filename);

        $extension = $info['extension'];

        $old_image = $filename;
        $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . $type .'.' . $extension;

        if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);

            if ($width_orig != $width || $height_orig != $height) {
                $image = new Image(DIR_IMAGE . $old_image);
                $image->resize($width, $height, $type);
                if ($width >= 500 || $height >= 500) {
                    $quality = 90;
                }
                else {
                    $quality = 75;
                }
                $image->save(DIR_IMAGE . $new_image, $quality);
            }
            else {
                copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
            }
        }

        // Prevent cache-anti-refresh which could show old images
        return $this->url->link('image/' . $new_image, filemtime(DIR_IMAGE . $new_image));
    }
}
