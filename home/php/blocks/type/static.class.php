<?php

namespace home;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use home\HomeManager;
use common\libraries\StringUtilities;
use common\libraries\Block;
use repository\RepositoryDataManager;
use repository\ContentObjectBlock;
use common\libraries\Application;

require_once CoreApplication :: get_application_class_path('repository') . 'blocks/content_object_block.class.php';

/**
 * A "Static" block. I.e. a block that display the title and description of an object.
 * Usefull to display free html text.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 * @package home.block
 */
class HomeStatic extends ContentObjectBlock {

    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        return Theme :: get_image_path(Application :: determine_namespace('home')) . 'static.png';
    }

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    static function get_supported_types() {
        $result = array();
        $result[] = \repository\content_object\announcement\Announcement::get_type_name();
        //$result[] = \repository\content_object\description\Description::get_type_name();
        $result[] = \repository\content_object\story\Story::get_type_name();
        $result[] = \repository\content_object\note\Note::get_type_name();

        return $result;
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