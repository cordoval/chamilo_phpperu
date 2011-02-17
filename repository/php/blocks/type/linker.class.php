<?php

namespace repository;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use home\HomeManager;
use common\libraries\Application;
use repository\content_object\link\Link;
use repository\content_object\rss_feed\RssFeed;
use common\libraries\StringUtilities;

/**
 * $Id: linker.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.block
 */
require_once CoreApplication :: get_application_class_path('repository') . 'blocks/repository_block.class.php';
require_once CoreApplication :: get_application_class_path('repository') . 'blocks/content_object_block.class.php';

class RepositoryLinker extends ContentObjectBlock {

    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        if ($type) {
            return parent::get_default_image_path($application, $type, $size);
        } else {
            return Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(Link:: get_type_name())) . 'logo/' . $size . '.png';
        }
    }

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    static function get_supported_types() {
        $result = array();
        $result[] = Link::get_type_name();
        return $result;
    }

    function __construct($parent, $block_info, $configuration) {
        parent::__construct($parent, $block_info, $configuration);
        $this->default_title = Translation::get('Linker');
    }

    function is_visible() {
        return true; //i.e.display on homepage when anonymous
    }

    /**
     * Returns the url to the icon.
     *
     * @return string
     */
    function get_icon() {
        return self::get_default_image_path();
    }

    function display_content() {
        $html = array();
        $content_object = $this->get_object();
        $url =  htmlentities($content_object->get_url());
        $target = $this->get_view() == self::WIDGET_VIEW ? ' target="_blank" ' : '';

        $html[] = $content_object->get_description();
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . $url . '" '.$target.'>' . $url . '</a></div>';

        return implode(StringUtilities::NEW_LINE, $html);
    }

}

?>