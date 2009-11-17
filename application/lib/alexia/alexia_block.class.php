<?php
/**
 * $Id: alexia_block.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia
 */
require_once Path :: get_library_path() . 'block.class.php';

class AlexiaBlock extends Block
{

    /**
     * Constructor.
     */
    function AlexiaBlock($parent, $block_info)
    {
        parent :: __construct($parent, $block_info);
    }

    static function factory($alexia, $block)
    {
        $type = $block->get_component();
        $filename = dirname(__FILE__) . '/block/alexia_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'Alexia' . Utilities :: underscores_to_camelcase($type);
        require_once $filename;
        return new $class($alexia, $block);
    }
}
?>