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
use repository\content_object\link\Link;
use repository\ContentObject;
use common\libraries\Application;

require_once CoreApplication :: get_application_class_path('repository') . 'blocks/content_object_block.class.php';

/**
 * An  "External" block. I.e. a block that displays a page's content in an iFrame.
 * Usefull to integrate external pages.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 * @package home.block
 */
class HomeExternal extends ContentObjectBlock {

    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        if ($type) {
            return Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(Link:: get_type_name())) . 'logo/' . $size .'.png';
        } else {
            return Theme :: get_image_path(Application :: determine_namespace('home')) . 'iframe.png';
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
        $default_title = Translation::get('External');
        parent::__construct($parent, $block_info, $configuration, $default_title);
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

    function get_min_height(){
        return 300;
    }

    function get_height(){
        $result = $this->get('height', '300');
        $resut = (int)$result;
        $result = max($this->get_min_height(), $result);
        return $result;
    }

    function display_content() {
        $height = '300px';
        $frameborder = '0';
        $scrolling = $this->get('scrolling', 'no');
        $src = $this->get_object() ? $this->get_object()->get_url() : '';
        $height = $this->get_height();

        $result = <<<EOT

        <iframe src="$src" width="100%" height="$height" frameborder="$frameborder" scrolling="$scrolling">
            <p>Your browser does not support iframes.</p>
        </iframe>

EOT;
        return $result;
    }

}

?>