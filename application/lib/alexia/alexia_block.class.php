<?php
/**
 * $Id: photo_gallery_block.class.php
 * @package application.lib.photo_gallery
 */
require_once Path :: get_library_path() . 'block.class.php';

class PhotoGalleryBlock extends Block
{

    /**
     * Constructor.
     */
    function PhotoGalleryBlock($parent, $block_info)
    {
        parent :: __construct($parent, $block_info);
    }

    static function factory($photo_gallery, $block)
    {
        $type = $block->get_component();
        $filename = dirname(__FILE__) . '/block/photo_gallery_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'PhotoGallery' . Utilities :: underscores_to_camelcase($type);
        require_once $filename;
        return new $class($photo_gallery, $block);
    }
}
?>