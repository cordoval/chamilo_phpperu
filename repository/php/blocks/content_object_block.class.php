<?php

namespace repository;

use common\libraries\Translation;
use common\libraries\StringUtilities;
use home\HomeManager;
use common\libraries\Theme;
use common\libraries\Application;

require_once dirname(__FILE__) . '/repository_block.class.php';

/**
 * Base class for blocks based on a content object.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class ContentObjectBlock extends RepositoryBlock {

    protected $default_title = '';

    function __construct($parent, $block_info, $configuration, $default_title = ''){
        parent::__construct($parent, $block_info, $configuration);
        $this->default_title = $default_title ? $default_title : Translation :: get('Object');
    }

    /**
     * The default's title value. That is the title to display when the block is not linked to a  content object.
     * 
     * @return string
     */
    protected function get_default_title(){
        return $this->default_title;
    }

    protected function set_default_title($value){
        $this->default_title = $value;
    }

    /**
     * If the block is linked to an object returns the object id. Otherwise returns 0.
     * 
     * @return int
     */
    function get_object_id() {
        return $this->get('use_object', 0);
    }

    /**
     * Return configuration property.
     *
     * @param string $name Name of the configuration property to retrieve
     * @param object $default Default value to return if property is not defined
     * @return object Configuration property value.
     */
    function get($name, $default = null){
        $configuration = $this->get_configuration();

        $result = isset($configuration[$name]) ? $configuration[$name] : null;
        return $result;
    }

    /**
     * If the block is linked to an object returns it. Otherwise returns null.
     *
     * @return ContentObject
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

    function as_html($view = '') {
        if (!$this->is_visible()) {
            return '';
        }
        if($view){
            $this->set_view($view);
        }
        
        $html = array();
        $html[] = $this->display_header();
        $html[] = $this->is_configured() ? $this->display_content() : $this->display_empty();
        $html[] = $this->display_footer();
        return implode(StringUtilities::NEW_LINE, $html);
    }

    /**
     * Returns the html to display when the block is not configured.
     *
     * @return string
     */
    function display_empty() {
        return Translation :: get('ConfigureBlockFirst', null, HomeManager :: APPLICATION_NAME);
    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    function display_content() {
        $content_object = $this->get_object();
        $display = ContentObjectDisplay :: factory($content_object);
        return $display->get_description();
    }

    /**
     * Returns the text title to display. That is the content's object title if the block is configured or the default title otherwise;
     *
     * @return string
     */
    function get_title(){
        $content_object = $this->get_object();
        return empty($content_object) ? $this->get_default_title() : $content_object->get_title();
    }

//    function display_title() {
//        $TITLE = $this->get_title();
//        $ACTIONS = $this->display_actions();
//
//        $result = $this->get_title_template();
//        return $this->process_template($result, get_defined_vars());
//    }
//
//    function get_title_template() {
//        $html = array();
//        $html[] = '<div class="title"><div style="float: left;">{$TITLE}</div>';
//        $html[] = '{$ACTIONS}';
//        $html[] = '<div style="clear: both;"></div>';
//        $html[] = '</div>';
//        return implode(StringUtilities::NEW_LINE, $html);
//    }
//
//    function display_header() {
//        $BLOCK_ID = $this->get_block_info()->get_id();
//        $ICON = $this->get_icon();
//        $STYLE = $this->get_block_info()->is_visible();
//        $TITLE = $this->display_title();
//
//        $result = $this->get_header_template();
//        return $this->process_template($result, get_defined_vars());
//    }
//
//    function get_header_template() {
//        $html = array();
//        $html[] = '<div class="block" id="block_{$BLOCK_ID}" style="background-image: url({$ICON});">';
//        $html[] = '{$TITLE}';
//        $html[] = '<div class="description" {$STYLE}>';
//        return implode(StringUtilities::NEW_LINE, $html);
//    }

    // BASIC TEMPLATING FUNCTIONS.

    //@TODO: remove that when we move to a templating system
    //@NOTE: could be more efficient to do an include or eval

    private $template_callback_context = array();

    protected function process_template($template, $vars){
        $pattern = '/\{\$[a-zA-Z_][a-zA-Z0-9_]*\}/';
        $this->template_callback_context = $vars;
        $template = preg_replace_callback($pattern, array($this, 'process_template_callback'), $template);
        return $template;
    }

    private function process_template_callback($matches){
        $vars = $this->template_callback_context;
        $name = trim($matches[0], '{$}');
        $result =  isset($vars[$name]) ? $vars[$name] : '';
        return $result;
    }
    
}

?>
