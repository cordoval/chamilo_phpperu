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

    /**
     * Cache for results of get_path() method.
     */
    private $path;

    function Theme()
    {
        $this->theme = PlatformSetting :: get('theme');
        $this->template = new Phpbb2TemplateWrapper($this->theme);
//        $this->template = ChamiloTemplate :: get_instance($this->theme);

        $this->path = array();
    }

    /**
     * Returns the template engine
     * @return Phpbb2TemplateWrapper
     */
    static function get_template()
    {
    	return self :: get_instance()->template;
    }

    static function get_theme()
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

        $this->path = array();
    }

    static function get_application()
    {
        return self :: get_instance()->application;
    }

    static function set_application($application)
    {
        $instance = self :: get_instance();
        $instance->application = $application;
    }

    function get_path($path_type)
    {
    	if (isset($this->path[$path_type]))
    	{
    		return $this->path[$path_type];
    	}

        switch ($path_type)
        {
            case WEB_IMG_PATH :
                return $this->path[$path_type] = Path :: get(WEB_LAYOUT_PATH) . $this->get_theme() . '/images/';
            case SYS_IMG_PATH :
                return $this->path[$path_type] = Path :: get(SYS_LAYOUT_PATH) . $this->get_theme() . '/images/';
            case WEB_CSS_PATH :
                return $this->path[$path_type] = Path :: get(WEB_LAYOUT_PATH) . $this->get_theme() . '/css/';
            case SYS_CSS_PATH :
                return $this->path[$path_type] = Path :: get(SYS_LAYOUT_PATH) . $this->get_theme() . '/css/';
            case WEB_THEME_PATH :
                return $this->path[$path_type] = Path :: get(WEB_LAYOUT_PATH) . $this->get_theme() . '/';
            case SYS_THEME_PATH :
                return $this->path[$path_type] = Path :: get(SYS_LAYOUT_PATH) . $this->get_theme() . '/';
        }
    }

    /**
     * Get the web path to the theme's folder
     */
    static function get_theme_path()
    {
        return self :: get_instance()->get_path(WEB_THEME_PATH);
    }

    /**
     * Get the web path to the application's css file
     */
    static function get_css_path()
    {
        $instance = self :: get_instance();
        $application = (is_null($application) ? $instance->get_application() : $application);
        return BasicApplication :: get_application_web_resources_css_path($application) . $instance->get_theme() . '.css';
    }

    /**
     * Get the web path to the general css file
     */
    static function get_common_css_path()
    {
        return self :: get_instance()->get_path(WEB_CSS_PATH) . 'common.css';
    }

    /**
     * Get the path to the application's image folder
     */
    static function get_image_path($application = null)
    {
        $instance = self :: get_instance();
        $application = (is_null($application) ? $instance->get_application() : $application);
        return BasicApplication :: get_application_web_resources_images_path($application) . $instance->get_theme() . '/';       
    }

    /**
     * Get the system path to the application's image folder
     */
    static function get_image_system_path($application = null)
    {
    	$instance = self :: get_instance();
        $application = (is_null($application) ? $instance->get_application() : $application);
        return BasicApplication :: get_application_resources_images_path($application) . $instance->get_theme() . '/';
    }

    /**
     * Get the path to the general image folder
     */
    static function get_common_image_path()
    {
        return self :: get_instance()->get_path(WEB_IMG_PATH) . 'common/';
    }

    /**
     * Get the system path to the general image folder
     */
    static function get_common_image_system_path()
    {
        return self :: get_instance()->get_path(SYS_IMG_PATH) . 'common/';
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    static function get_themes()
    {
        $options = array();

        $path = Path :: get(SYS_LAYOUT_PATH);
        $directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);

        foreach ($directories as $index => & $directory)
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
        $icon = new ToolbarItem($label, self :: get_common_image_path() . $image . '.' . $extension, $href, $display, $confirmation);
        return $icon->as_html();
    }

    static function get_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        $icon = new ToolbarItem($label, self :: get_image_path() . $image . '.' . $extension, $href, $display, $confirmation);
        return $icon->as_html();
    }

    static function get_content_object_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        return self :: get_common_image('content_object/' . $image, $extension, $label, $href, $display, $confirmation);
    }

    static function get_treemenu_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        return self :: get_common_image('treemenu/' . $image, $extension, $label, $href, $display, $confirmation);
    }

    static function get_treemenu_type_image($image, $extension = 'png', $label = null, $href = null, $display = ToolbarItem :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
        return self :: get_common_image('treemenu_types/' . $image, $extension, $label, $href, $display, $confirmation);
    }
}
?>