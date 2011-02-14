<?php

namespace common\libraries;

use home\HomeManager;
use common\libraries\StringUtilities;

/**
 * $Id: block.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */
class Block {
    const PARAM_ACTION = 'block_action';
    const BLOCK_LIST_SIMPLE = 'simple';
    const BLOCK_LIST_ADVANCED = 'advanced';

    const BLOCK_VIEW = 'block_view'; // display the block view for integration into chamil's home page
    const WIDGET_VIEW = 'widget_view'; // dispay the widget view for integration into a third party application such as a portal

    /**
     *
     * @param HomeRenderer $renderer
     * @param HomeBlock $block_info
     * @return Block
     */
    static public function factory(HomeRenderer $renderer, HomeBlock $block_info) {
        $type = $block_info->get_component();
        $application = $block_info->get_application();

        $path = BasicApplication :: get_application_class_path($application) . 'blocks/type/' . $type . '.class.php';
        if (!file_exists($path) || !is_file($path)) {
            die('Failed to load "' . $type . '" block');
        }
        $application_type = Application :: get_application_type($application);
        $class = $application_type :: get_application_namespace($application) . '\\' . Utilities :: underscores_to_camelcase($application . '_' . $type);
        require_once $path;
        return new $class($renderer, $block_info);
    }

    /**
     * Returns the default image to be displayed for block's creation. Can be redefined in subclasses to change the default icon.
     *
     * @todo: not so good. would be better to make the whole "create block" a view.
     *
     * @param string $application
     * @param string $type
     * @param string $size
     * @return string
     */
    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        if (empty($type)) {
            if ($application) {
                return Theme :: get_image_path(Application :: determine_namespace($application)) . 'logo/' . $size . '.png';
            } else {
                return Theme :: get_image_path(Application :: determine_namespace('home')) . 'logo/' . $size . '.png';
            }
        }

        $path = BasicApplication :: get_application_class_path($application) . 'blocks/type/' . $type . '.class.php';
        if (!file_exists($path) || !is_file($path)) {
            die('Failed to load "' . $type . '" block ' . $application);
        }
        $application_type = Application :: get_application_type($application);
        $class = $application_type :: get_application_namespace($application) . '\\' . Utilities :: underscores_to_camelcase($application . '_' . $type);
        require_once $path;

        return $class::get_default_image_path($application, '', $size);
    }

    private $parent;
    private $type;
    private $block_info;
    private $configuration;
    private $view = self::BLOCK_VIEW;

    function __construct($parent, $block_info) {
        $this->parent = $parent;
        $this->block_info = $block_info;
        $this->configuration = $block_info->get_configuration();
    }

    /**
     * The type of view: block or widget. Block - default - is for integration into Chamilo's homepage. Widget is for integration into a third party application - i.e. external portal.
     * @return string
     */
    function get_view() {
        return $this->view;
    }

    function set_view($view) {
        $this->view = $view;
    }

    /**
     * Returns the tool which created this publisher.
     * @return Tool The tool.
     */
    function get_parent() {
        return $this->parent;
    }

    function get_configuration() {
        return $this->configuration;
    }

    /**
     * @see Tool::get_user_id()
     */
    function get_user_id() {
        return $this->get_parent()->get_user_id();
    }

    function get_user() {
        return $this->get_parent()->get_user();
    }

    /**
     * Returns the types of learning object that this object may publish.
     * @return array The types.
     */
    function get_type() {
        return $this->type;
    }

    function get_block_info() {
        return $this->block_info;
    }

    function is_editable() {
        return true;
    }

    function is_hidable() {
        return true;
    }

    function is_deletable() {
        return true;
    }

    /**
     * Returns true if the block is to be displayed, false otherwise. By default do not show on home page when user is not connected.
     *
     * @return bool
     */
    function is_visible() {
        return Session::get_user_id() != 0;
    }

    /**
     * Returns the block's title to display.
     *
     * @return string
     */
    function get_title() {
        return $this->get_block_info()->get_title();
    }

    /**
     * Returns the url to the icon.
     *
     * @return string
     */
    function get_icon() {
        return Theme :: get_image_path(Application :: determine_namespace($this->get_block_info()->get_application())) . 'logo/' . Theme :: ICON_MEDIUM . '.png';
    }

    function as_html($view = '') {
        if (!$this->is_visible()) {
            return '';
        }
        if ($view) {
            $this->set_view($view);
        }

        $config = $this->get_configuration();

        $html = array();
        $html[] = $this->display_header();
        $html[] = $this->display_content();
        $html[] = $this->display_footer();

        return implode(StringUtilities::NEW_LINE, $html);
    }

    function display_header() {

        $block_id = $this->get_block_info()->get_id();
        $icon_url = $this->get_icon();
        $widget_view_url = $this->get_block_widget_view_link($this->block_info);
        $title = $this->display_title();
        if ($this->get_view() == self::BLOCK_VIEW) {//i.e. in widget view it is the portal configuration that decides to show/hide
            $description_style = $this->get_block_info()->is_visible() ? '' : ' style="display: none"';
        } else {
            $description_style = '';
        }

        $html = array();
        $html[] = '<div class="block hslice" id="block_' . $block_id . '" style="background-image: url(' . $icon_url . ');">';
        $html[] = $title;
        //support for IE web slices
        $html[] = '<span class="ttl" style="display:none;">15</span>'; //refresh 15 minutes
        $html[] = '<a rel="entry-content" href="' . $widget_view_url . '" style="display:none;"></a>';
        //$html[] = '<a rel="bookmark" href="'. $this->get_url().'" style="display:none;"></a>';
        $html[] = '<div class="description"' . $description_style . '>';

        return implode("\n", $html);
    }

    function display_title() {

        $title = htmlspecialchars($this->get_title());
        $actions = $this->display_actions();

        $html = array();
        $html[] = '<div class="title entry-title"><div style="float: left;">' . $title . '</div>';
        $html[] = $actions;
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    function display_actions() {
        if ($this->get_view() != self::BLOCK_VIEW) {
            return '';
        }

        $html = array();

        $user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

        $img_path = htmlspecialchars(Theme :: get_common_image_path());
        if (($user_home_allowed && Authentication :: is_valid()) || (!is_null($this->get_user()) && $this->get_user()->is_platform_admin())) {
            if ($this->is_deletable()) {
                $html[] = '<a href="' . htmlspecialchars($this->get_block_deleting_link($this->get_block_info())) . '" class="deleteEl"><img src="' . $img_path . 'action_delete.png" /></a>';
            }

            if ($this->block_info->is_configurable()) {
                $html[] = '<a href="' . htmlspecialchars($this->get_block_configuring_link($this->get_block_info())) . '" class="configEl"><img src="' . $img_path . 'action_config.png" /></a>';
            }

            if ($this->is_editable()) {
                $html[] = '<a href="' . htmlspecialchars($this->get_block_editing_link($this->get_block_info())) . '" class="editEl"><img src="' . $img_path . 'action_edit.png" /></a>';
            }

            if ($this->is_hidable()) {
                $html[] = '<a href="' . htmlspecialchars($this->get_block_visibility_link($this->get_block_info())) . '" class="closeEl"><img class="visible"' . ($this->get_block_info()->is_visible() ? '' : ' style="display: none;"') . ' src="' . $img_path . 'action_visible.png" /><img class="invisible"' . ($this->get_block_info()->is_visible() ? ' style="display: none;"' : '') . ' src="' . $img_path . 'action_invisible.png" /></a>';
            }

            $html[] = '<a href="#" id="drag_block_' . $this->get_block_info()->get_id() . '" class="dragEl"><img src="' . $img_path . 'action_drag.png" /></a>';
        }

        return implode("\n", $html);
    }

    function display_content() {
        return '';
    }

    function display_footer() {
        $html = array();

        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * Returns the URL used to view a block in "WIDGET" mode. I.e. for integration into an external application - web portal, IE webslice, etc.
     *
     * @param HomeBlock $home_block
     * @return string
     */
    function get_block_widget_view_link($home_block) {
        return $this->get_link(HomeManager :: APPLICATION_NAME, array(
            HomeManager :: PARAM_ACTION => HomeManager :: ACTION_WIDGET_VIEWER,
            HomeManager :: PARAM_HOME_TYPE => HomeManager :: TYPE_BLOCK,
            HomeManager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    function get_block_visibility_link($home_block) {
        return $this->get_link(HomeManager :: APPLICATION_NAME, array(
            HomeManager :: PARAM_ACTION => HomeManager :: ACTION_EDIT_HOME,
            HomeManager :: PARAM_HOME_TYPE => HomeManager :: TYPE_BLOCK,
            HomeManager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    function get_block_deleting_link($home_block) {
        return '#';
    }

    function get_block_editing_link($home_block) {
        return $this->get_link(HomeManager :: APPLICATION_NAME, array(
            HomeManager :: PARAM_ACTION => HomeManager :: ACTION_EDIT_HOME,
            HomeManager :: PARAM_HOME_TYPE => HomeManager :: TYPE_BLOCK,
            HomeManager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    function get_block_configuring_link($home_block) {
        return $this->get_link(HomeManager :: APPLICATION_NAME, array(
            HomeManager :: PARAM_ACTION => HomeManager :: ACTION_CONFIGURE_HOME,
            HomeManager :: PARAM_HOME_TYPE => HomeManager :: TYPE_BLOCK,
            HomeManager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    public function get_link($application, $parameters = array(), $encode = false) {
        return Redirect :: get_link($application, $parameters, array(), $encode, Redirect :: TYPE_CORE);
    }

    function get_platform_blocks($type = self :: BLOCK_LIST_SIMPLE) {
        $result = array();
        $applications_options = array();
        $components_options = array();
        $images_options = array();

        $applications = WebApplication :: load_all(false);

        foreach ($applications as $application) {
            $application_path = WebApplication :: get_application_class_path($application) . 'blocks/type/';
            if ($handle = opendir($application_path)) {
                while (false !== ($file = readdir($handle))) {
                    if (!is_dir($file) && stripos($file, '.class.php') !== false) {
                        $component = str_replace('.class.php', '', $file);

                        $applications_options[$application] = Translation :: get(Application :: application_to_class($application), null, Application :: determine_namespace($application));
                        $components_options[$application][$component] = Utilities :: underscores_to_camelcase($component);
                        $images_options[$application][$component] = self::get_default_image_path($application, $component);
                    }
                }
                closedir($handle);
            }
        }

        $core_applications = array('admin', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu');

        $path = Path :: get(SYS_PATH);

        foreach ($core_applications as $core_application) {
            $application_path = CoreApplication :: get_application_class_path($core_application) . 'blocks/type/';
            if ($handle = opendir($application_path)) {
                while (false !== ($file = readdir($handle))) {
                    if (!is_dir($file) && stripos($file, '.class.php') !== false) {

                        $component = str_replace('.class.php', '', $file);

                        $applications_options[$core_application] = Translation :: get(Application :: application_to_class($core_application), null, Application :: determine_namespace($core_application));
                        $components_options[$core_application][$component] = Utilities :: underscores_to_camelcase($component);
                        $images_options[$core_application][$component] = self::get_default_image_path($core_application, $component);
                    }
                }
                closedir($handle);
            }
        }

        asort($applications_options);

        $result['applications'] = $applications_options;
        $result['components'] = $components_options;
        $result['images'] = $images_options;
        return $result;
    }

    /*
     * We keep this since Quickform's hierselect element
     * only works if javascript is enabled
     */

    function get_platform_blocks_deprecated() {
        $application_components = array();
        $applications = WebApplication :: load_all(false);

        $path = Path :: get_application_path() . '/lib/';

        foreach ($applications as $application) {
            $application_path = WebApplication :: get_application_class_path($application) . 'blocks/type/';
            if ($handle = opendir($application_path)) {
                while (false !== ($file = readdir($handle))) {
                    if (!is_dir($file) && stripos($file, '.class.php') !== false) {
                        $component = str_replace('.class.php', '', $file);
                        $component = str_replace($application . '_', '', $component);
                        $value = $application . '.' . $component;
                        $display = Translation :: get(Application :: application_to_class($application)) . '&nbsp;>&nbsp;' . Utilities :: underscores_to_camelcase($component);
                        $application_components[$value] = $display;
                    }
                }
                closedir($handle);
            }
        }

        $core_applications = array('admin', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu');

        $path = Path :: get(SYS_PATH);

        foreach ($core_applications as $core_application) {
            $application_path = CoreApplication :: get_application_class_path($core_application) . 'blocks/type/';
            if ($handle = opendir($application_path)) {
                while (false !== ($file = readdir($handle))) {
                    if (!is_dir($file) && stripos($file, '.class.php') !== false) {
                        $component = str_replace('.class.php', '', $file);
                        $component = str_replace($core_application . '_', '', $component);
                        $value = $core_application . '.' . $component;
                        $display = Translation :: get(Application :: application_to_class($core_application)) . '&nbsp;>&nbsp;' . Utilities :: underscores_to_camelcase($component);
                        $application_components[$value] = $display;
                    }
                }
                closedir($handle);
            }
        }

        asort($application_components);

        return $application_components;
    }

    function get_url($parameters = array(), $filter = array(), $encode_entities = false) {
        return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
    }

    function get_parameter($name) {
        return $this->get_parent()->get_parameter($name);
    }

}

?>