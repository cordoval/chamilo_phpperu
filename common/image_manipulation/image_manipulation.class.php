<?php
/**
 * $Id: image_manipulation.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.image_manipulation
 */
/**
 * An abstract class for handling image manipulations. Impement new image
 * manipulation methods by creating a class which extends this abstract class.
 */
abstract class ImageManipulation
{
    /**
     * When cropping an image, use this offset value to get the exacte center of
     * the image
     */
    const CROP_CENTER = - 1;
    /**
     * Final dimensions will be less than or equal to the entered width and
     * height. Useful for ensuring a maximum height and/or width.
     */
    const SCALE_INSIDE = 0;
    /**
     * Final dimensions will be greater than or equal to the entered width and
     * height. Ideal for cropping the result to a square.
     */
    const SCALE_OUTSIDE = 1;
    /**
     * The file on which the manipulations will be done
     */
    protected $source_file;

    /**
     * Constructor
     * @param string $source_file Full path of the image file on which the
     * manipulations should be done
     */
    function ImageManipulation($source_file)
    {
        $this->source_file = $source_file;
        $dimension = getimagesize($source_file);
        $this->width = $dimension[0];
        $this->height = $dimension[1];
    }

    /**
     * Resize an image maintaining the original aspect-ratio. Images which are
     * allready smaller than the given width and height won't be scaled.
     * @param int $width
     * @param int $height
     * @param int $type
     * @return boolean True if successfull, false if not
     */
    function scale($width, $height, $type = ImageManipulation :: SCALE_INSIDE)
    {
        $aspect = $this->height / $this->width;
        if ($type == ImageManipulation :: SCALE_OUTSIDE)
        {
            $new_aspect = $height / $width;
            $width = ($aspect < $new_aspect ? 9999999 : $width);
            $height = ($aspect > $new_aspect ? 9999999 : $height);
        }
        // don't scale up
        if ($width >= $this->width && $height >= $this->height)
        {
            return false;
        }
        $new_aspect = $height / $width;
        if ($aspect < $new_aspect)
        {
            $width = (int) min($width, $this->width);
            $height = (int) round($width * $aspect);
        }
        else
        {
            $height = (int) min($height, $this->height);
            $width = (int) round($height / $aspect);
        }
        return $this->resize($width, $height);
    }

    /**
     * Creates a thumbnail from by rescaling the image to the given width &
     * height (using the SCALE_OUTSIDE parameter). After this, the resulting
     * image will be cropped. The result is an image which the exact given with
     * and height.
     * @param int $width With of the resulting image
     * @param int $height Height of the resulting image (if null, the height
     * will be the same as the width, resulting in a square image)
     * @return boolean True if successfull, false if not
     */
    function create_thumbnail($width, $height = null)
    {
        if (is_null($height))
        {
            $height = $width;
        }
        if ($this->scale($width, $height, ImageManipulation :: SCALE_OUTSIDE))
        {
            return $this->crop($width, $height);
        }
        return false;
    }

    /**
     * Crop an image to the rectangle specified by the given offsets and
     * dimensions.
     * @param int $width The width of the image after cropping
     * @param int $height The height of the image after cropping
     * @param int $offset_x
     * @param int $offset_y
     * @return boolean True if successfull, false if not
     */
    abstract function crop($width, $height, $offset_x = ImageManipulation :: CROP_CENTER, $offset_y = ImageManipulation :: CROP_CENTER);

    /**
     * Resize an image to an exact set of dimensions, ignoring aspect ratio.
     * @param int $width The width of the image after resizing
     * @param int $height The height of the image after resizing
     * @return boolean True if successfull, false if not
     */
    abstract function resize($width, $height);

    /**
     * Write the resulting image (after some manipulations to a file)
     * @param string $source_file Full path of the file to which the image should be
     * written. If null, the original image will be overwritten.
     * @return boolean True if successfull, false if not
     */
    abstract function write_to_file($source_file = null);

    /**
     * Create an imagemanipulation instance
     * @todo At the moment this returns the class using GD. The class to return
     * should be configurable
     * @param string $source_file Full path of the image file on which the
     * manipulations should be done
     */
    public static function factory($source_file)
    {
        require_once dirname(__FILE__) . '/gd/gd_image_manipulation.class.php';
        return new GdImageManipulation($source_file);
    }

    /**
     * Gets the image extension from the source file.
     * @return string
     */
    protected function get_image_extension()
    {
        $info = getimagesize($this->source_file);
        $extensions = array('1' => 'gif', '2' => 'jpg', '3' => 'png');
        $extension = array_key_exists($info[2], $extensions) ? $extensions[$info[2]] : '';
        return $extension;
    }
}
?>