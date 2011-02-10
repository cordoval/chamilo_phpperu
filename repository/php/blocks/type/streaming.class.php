<?php

namespace repository;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use home\HomeManager;
use repository\content_object\rss_feed\RssFeed;
use common\libraries\Application;
use repository\content_object\dailymotion\Dailymotion;
use repository\content_object\youtube\Youtube;
use repository\content_object\matterhorn\Matterhorn;
use repository\content_object\mediamosa\Mediamosa;
use repository\content_object\slideshare\Slideshare;
use repository\content_object\soundcloud\Soundcloud;
use repository\content_object\vimeo\Vimeo;

/**
 * @package repository.block
 */
require_once CoreApplication :: get_application_class_path('repository') . 'blocks/content_object_block.class.php';

/**
 * Block to display streaming media.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class RepositoryStreaming extends ContentObjectBlock {

    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        if ($type) {
            return parent::get_default_image_path($application, $type, $size);
        } else {
            return Theme :: get_image_path(Application :: determine_namespace('repository')) . 'media_32.png';
        }
    }

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    static function get_supported_types() {
        $result = array();
        $result[] = Dailymotion::get_type_name();
        $result[] = Matterhorn::get_type_name();
        $result[] = Mediamosa::get_type_name();
        $result[] = Slideshare::get_type_name();
        $result[] = Soundcloud::get_type_name();
        $result[] = Vimeo::get_type_name();
        $result[] = Youtube::get_type_name();

        return $result;
    }

    function __construct($parent, $block_info) {
        parent::__construct($parent, $block_info);
        $this->default_title = Translation::get('Streaming');
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

}

?>