<?php
/**
 * $Id: theme.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html
 */
// Theme-paths
define('WEB_THEME_PATH', 'WEB_THEME_PATH');
define('SYS_THEME_PATH', 'SYS_THEME_PATH');
define('WEB_IMG_PATH', 'WEB_IMG_PATH');
define('SYS_IMG_PATH', 'SYS_IMG_PATH');
define('WEB_CSS_PATH', 'WEB_CSS_PATH');
define('SYS_CSS_PATH', 'SYS_CSS_PATH');

class Theme
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * The theme we're currently using
     */
    private $theme;

    /**
     * The application we're currently rendering
     */
    private $application;

    /**
     * The template engine
     * @var ChamiloTemplate The chamilo templating object
     */
    private $template;

    function Theme()
    {
        $this->theme = PlatformSetting :: get('theme');
        $this->template = new Phpbb2TemplateWrapper($this->theme);
//        $this->template = ChamiloTemplate :: get_instance($this->theme);
    }

    /**
     * Returns the template engine
     * @return Phpbb2TemplateWrapper
     */
    static function get_template()
    {
    	return self :: get_instance()->template;
    }

    function get_theme()
    {
        return self :: get_instance()->theme;
    }

    function set_theme($theme)
    {
        $instance = self :: get_instance();
        $instance->theme = $theme;

        $template = $instance->get_template();
        $template->set_theme($theme);
        $template->reset();
    }

    function get_application()
    {
        return self :: get_instance()->application;
    }

    function set_application($application)
    {
        $instance = self :: get_instance();
        $instance->application = $application;
    }

    function get_path($path_type)
    {
        switch ($path_type)
        {
            case WEB_IMG_PATH :
                return Path :: get(WEB_LAYOUT_PATH) . $this->get_theme() . '/images/';
            case SYS_IMG_PATH :
                return Path :: get(SYS_LAYOUT_PATH) . $this->get_theme() . '/images/';
            case WEB_CSS_PATH :
                return Path :: get(WEB_LAYOUT_PATH) . $this->get_theme() . '/css/';
            case SYS_CSS_PATH :
                return Path :: get(SYS_LAYOUT_PATH) . $this->get_theme() . '/css/';
            case WEB_THEME_PATH :
                return Path :: get(WEB_LAYOUT_PATH) . $this->get_theme() . '/';
            case SYS_THEME_PATH :
                return Path :: get(SYS_LAYOUT_PATH) . $this->get_theme() . '/';
        }
    }

    /**
     * Get the web path to the theme's folder
     */
    function get_theme_path()
    {
        $instance = self :: get_instance();
        return $instance->get_path(WEB_THEME_PATH);
    }

    /**
     * Get the web path to the application's css file
     */
    function get_css_path()
    {
        $instance = self :: get_instance();
        return $instance->get_path(WEB_CSS_PATH) . $instance->get_application() . '.css';
    }

    /**
     * Get the web path to the general css file
     */
    function get_common_css_path()
    {
        $instance = self :: get_instance();
        return $instance->get_path(WEB_CSS_PATH) . 'common.css';
    }

    /**
     * Get the path to the application's image folder
     */
    static function get_image_path($application = null)
    {
        $instance = self :: get_instance();
        $application = (is_null($application) ? $instance->get_application() : $application);
        return $instance->get_path(WEB_IMG_PATH) . $application . '/';
    }

    /**
     * Get the path to the general image folder
     */
    static function get_common_image_path()
    {
        $instance = self :: get_instance();
        return $instance->get_path(WEB_IMG_PATH) . 'common/';
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    function get_themes()
    {
        $options = array();

        $path = Path :: get(SYS_LAYOUT_PATH);
        $directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);

        foreach ($directories as $index => $directory)
        {
            if (substr($directory, 0, 1) != '.')
            {
                $options[$directory] = Utilities :: underscores_to_camelcase($directory);
            }
        }

        return $options;
    }

    static function get_common_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        $image = self :: get_common_image_path() . $image . '.' . $extension;

        $icon = new ToolbarItem($label, $image, $href, $display, $confirmation);
        return $icon->as_html();
    }

    static function get_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        $image = self :: get_image_path() . $image . '.' . $extension;

        $icon = new ToolbarItem($label, $image, $href, $display, $confirmation);
        return $icon->as_html();
    }

    function get_content_object_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        $image = 'content_object/' . $image;
        return self :: get_common_image($image, $extension, $label, $href, $display, $confirmation);
    }

    function get_treemenu_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        $image = 'treemenu/' . $image;
        return self :: get_common_image($image, $extension, $label, $href, $display, $confirmation);
    }

    function get_treemenu_type_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        $image = 'treemenu_types/' . $image;
        return self :: get_common_image($image, $extension, $label, $href, $display, $confirmation);
    }
}
?>