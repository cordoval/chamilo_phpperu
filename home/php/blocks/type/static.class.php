<?php

namespace home;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use home\HomeManager;
use common\libraries\StringUtilities;
use common\libraries\Block;
use repository\RepositoryDataManager;

/**
 * A "Static" block. I.e. a block that display the title and description of an object.
 * Usefull to display free html text.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 * @package home.block
 */
class HomeStatic extends Block {

    function is_visible()
    {
        return true; //i.e.display on homepage when anonymous
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

    /**
     * If the block is linked to an object returns the object id. Otherwise returns 0.
     * @return int
     */
    function get_object_id() {
        $configuration = $this->get_configuration();

        $result = isset($configuration['use_object']) ? $configuration['use_object'] : 0;
        $result = empty($result) ? 0 : $result;
        return $result;
    }

    /**
     * If the block is linked to an object returns it. Otherwise returns null.
     *
     * @return object
     */
    function get_object() {
        $object_id = $this->get_object_id();
        if ($object_id == 0) {
            return null;
        } else {
            return RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
        }
    }

    /**
     * Return true if the block is linked to an object. Otherwise returns false.
     *
     * @return bool
     */
    function is_configured() {
        $object_id = $this->get_object_id();
        return $object_id != 0;
    }

    function as_html() {
        if ($this->is_configured()) {
            return $this->display_content();
        } else {
            return $this->display_empty();
        }
    }

    /**
     * Returns the html to display when the block is not configured.
     *
     * @return string
     */
    function display_empty() {
        $html[] = $this->display_header();
        $html[] = Translation :: get('ConfigureBlockFirst', null, HomeManager :: APPLICATION_NAME);
        $html[] = $this->display_footer();
        return implode(StringUtilities::NEW_LINE, $html);
    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    function display_content() {
        $content_object = $this->get_object();

        $BLOCK_ID = $this->get_block_info()->get_id();
        $ICON = Theme :: get_image_path() . 'block_' . $this->get_block_info()->get_application() . '.png';
        $TITLE = $content_object->get_title();
        $ACTIONS = $this->display_actions();
        $DISPLAY = $this->get_block_info()->is_visible() ? '' : ' style="display: none"';
        $DESCRIPTION = $content_object->get_description();
        
        $result = $this->get_content_template();

        $result = str_ireplace('{$BLOCK_ID}', $BLOCK_ID, $result);
        $result = str_ireplace('{$ICON}', $ICON, $result);
        $result = str_ireplace('{$TITLE}', $TITLE, $result);
        $result = str_ireplace('{$ACTIONS}', $ACTIONS, $result);
        $result = str_ireplace('{$DISPLAY}', $DISPLAY, $result);
        $result = str_ireplace('{$DESCRIPTION}', $DESCRIPTION, $result);

        return $result;
    }

    function get_content_template(){

        $html[] = '<div class="block" id="block_{$BLOCK_ID}" style="background-image: url({$ICON});">';
        $html[] = '<div class="title"><div style="float: left;">{$TITLE}</div>';
        $html[] = '{$ACTIONS}';
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '<div class="description" {$DISPLAY}>';
        $html[] = '{$DESCRIPTION}';
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $result = implode(StringUtilities::NEW_LINE, $html);
        return $result;
    }

    function is_hidable() {
        return false;
    }

}

?>