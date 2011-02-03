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
use repository\content_object\twitter_search\TwitterSearch;
use repository\ContentObjectDisplay;
use repository\ContentObject;

require_once CoreApplication :: get_application_class_path('repository') . 'blocks/content_object_block.class.php';

/**
 * Description of twitter
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent opprecht
 */
class HomeTwitterSearch extends ContentObjectBlock {

    function __construct($parent, $block_info) {
        parent::__construct($parent, $block_info);
        $this->default_title = Translation :: get('TwitterSearch');
    }

    /**
     * Returns the url to the icon.
     *
     * @return string
     */
    function get_icon() {
        return Theme::get_content_object_image_path(TwitterSearch::get_type_name(), Theme::ICON_BIG);
    }

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    static function get_supported_types() {
        $result = array();
        $result[] = TwitterSearch::get_type_name();
        return $result;
    }

    function is_visible() {
        return true; //i.e.display on homepage when anonymous
    }

    function get_scrollbar() {
        return (bool) $this->get('scrollbar');
    }

    function get_loop() {
        return (bool) $this->get('loop');
    }

    function get_live() {
        return (bool) $this->get('live');
    }

    function get_hashtags() {
        return (bool) $this->get('hashtags');
    }

    function get_timestamp() {
        return (bool) $this->get('timestamp');
    }

    function get_avatars() {
        return (bool) $this->get('avatars');
    }

    function get_toptweets() {
        return (bool) $this->get('toptweets');
    }

    function display_content() {
        $content_object = $this->get_object();
        $display = ContentObjectDisplay :: factory($content_object);
        $scrollbar = $this->get_scrollbar();
        $loop = $this->get_loop();
        $live = $this->get_live();
        $hashtags = $this->get_hashtags();
        $timestamp = $this->get_timestamp();
        $avatars = $this->get_avatars();
        $toptweets = $this->get_toptweets();
        return $display->get_widget_html($scrollbar, $loop, $liv, $hashtags, $timestamp, $avatars, $toptweets);
    }

}

?>
