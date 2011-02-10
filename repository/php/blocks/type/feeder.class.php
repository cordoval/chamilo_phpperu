<?php

namespace repository;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use common\libraries\StringUtilities;
use home\HomeManager;
use repository\content_object\rss_feed\RssFeed;

/**
 * $Id: feeder.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.block
 */
require_once CoreApplication :: get_application_class_path('repository') . 'blocks/repository_block.class.php';
require_once CoreApplication :: get_application_class_path('repository') . 'blocks/content_object_block.class.php';

/**
 * Display a RSS Feed.
 *
 */
class RepositoryFeeder extends ContentObjectBlock {

    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        if ($type) {
            return parent::get_default_image_path($application, $type, $size);
        } else {
            return Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(RssFeed :: get_type_name())) . 'logo/' . $size . '.png';
        }
    }

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    static function get_supported_types() {
        $result = array();
        $result[] = RssFeed::get_type_name();
        return $result;
    }

    function __construct($parent, $block_info) {
        parent::__construct($parent, $block_info);
        $this->default_title = Translation::get('Newsfeed');
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

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    function display_content() {
        $content_object = $this->get_object();
        $display = ContentObjectDisplay :: factory($content_object);

        $html = array();
        $feed = $display->parse_file($content_object->get_url());
        if ($feed) {
            $icon = self::get_default_image_path('', '', Theme :: ICON_MINI);
            $html[] = '<br /><div class="tool_menu">';
            $html[] = '<ul>';
            foreach ($feed['items'] as $item) {
                $html[] = '<li class="tool_list_menu" style="background-image: url('. $icon . ')"><a href="' . htmlentities($item['link']) . '">' . $item['title'] . '</a></li>';
            }
            $html[] = '</ul>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
        return implode(StringUtilities::NEW_LINE, $html);
    }

}

?>