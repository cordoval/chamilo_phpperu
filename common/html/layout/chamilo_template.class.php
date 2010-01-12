<?php
require_once Path :: get_plugin_path() . 'phpbb3/phpbb3_template.php';
require_once Path :: get_library_path() . 'html/layout/chamilo_template_compiler.class.php';

define('WEB_TPL_PATH', 'WEB_TPL_PATH');
define('SYS_TPL_PATH', 'SYS_TPL_PATH');

class ChamiloTemplate extends template
{
    private static $instance;

    function __construct()
    {
        $this->set_template();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    function get_configuration()
    {
        return array();
    }

    /**
     * Set template location
     * @access public
     */
    function set_template()
    {
        // TODO: What if these paths don't exist?
        $this->root = self :: get_template_path();
        $this->cachepath = self :: get_cache_path();

        if (! file_exists($this->root))
        {
            trigger_error('Template path could not be found: ' . $this->root, E_USER_ERROR);
        }

        $this->_rootref = &$this->_tpldata['.'][0];

        return true;
    }

    /**
     * Sets the template filenames for handles. $filename_array
     * should be a hash of handle => filename pairs.
     * @access public
     */
    function set_filenames($filename_array)
    {
        if (! is_array($filename_array))
        {
            return false;
        }
        foreach ($filename_array as $handle => $filename)
        {
            if (empty($filename))
            {
                trigger_error('ChamiloTemplate->set_filenames: Empty filename specified for' . $handle, E_USER_ERROR);
            }

            $this->filename[$handle] = $filename;
            $this->files[$handle] = $this->root . $filename;
        }

        return true;
    }

    /**
     * Display handle
     * @access public
     */
    function display($handle, $include_once = true)
    {
        if (defined('IN_ERROR_HANDLER'))
        {
            if ((E_NOTICE & error_reporting()) == E_NOTICE)
            {
                error_reporting(error_reporting() ^ E_NOTICE);
            }
        }

        if ($filename = $this->_tpl_load($handle))
        {
            ($include_once) ? include_once ($filename) : include ($filename);
        }
        else
        {
            eval(' ?>' . $this->compiled_code[$handle] . '<?php ');
        }

        return true;
    }

    /**
     * Load a compiled template if possible, if not, recompile it
     * @access private
     */
    function _tpl_load(&$handle)
    {
        if (! isset($this->filename[$handle]))
        {
            trigger_error('ChamiloTemplate->_tpl_load(): No file specified for handle' . $handle, E_USER_ERROR);
        }

        $filename = $this->cachepath . str_replace('/', '.', $this->filename[$handle]) . '.php';

        $recompile = false;
        if (! file_exists($filename) || @filesize($filename) === 0)
        {
            $recompile = true;
        }
        else
        {
            $reload_templates = PlatformSetting :: get('reload_templates');
            if ($reload_templates)
            {
                $recompile = (@filemtime($filename) < filemtime($this->files[$handle])) ? true : false;
            }
        }

        // Recompile page if the original template is newer, otherwise load the compiled version
        if (! $recompile)
        {
            return $filename;
        }

        $compile = new ChamiloTemplateCompiler($this);

        // If we don't have a file assigned to this handle, die.
        if (! isset($this->files[$handle]))
        {
            trigger_error('ChamiloTemplate->_tpl_load(): No file specified for handle ' . $handle, E_USER_ERROR);
        }

        $compile->_tpl_load_file($handle);
        return false;
    }

    /**
     * Include a separate template
     * @access private
     */
    function _tpl_include($filename, $include = true)
    {
        $handle = $filename;
        $this->filename[$handle] = $filename;
        $this->files[$handle] = $this->root . '/' . $filename;

        $filename = $this->_tpl_load($handle);

        if ($include)
        {
            if ($filename)
            {
                include ($filename);
                return;
            }
            eval(' ?>' . $this->compiled_code[$handle] . '<?php ');
        }
    }

    /**
     * Include a php-file
     * @access private
     */
    function _php_include($filename)
    {
        $file = Path :: get(SYS_PATH) . $filename;

        if (! file_exists($file))
        {
            // trigger_error cannot be used here, as the output already started
            echo 'ChamiloTemplate->_php_include(): File ' . htmlspecialchars($file) . ' does not exist or is empty';
            return;
        }
        include ($file);
    }

    function get_path($path_type)
    {
        switch ($path_type)
        {
            case WEB_TPL_PATH :
                return Path :: get(WEB_LAYOUT_PATH) . Theme :: get_theme() . '/templates/';
            case SYS_TPL_PATH :
                return Path :: get(SYS_LAYOUT_PATH) . Theme :: get_theme() . '/templates/';
        }
    }

    /**
     * Get the path to the theme's template folder.
     */
    function get_template_path()
    {
        return self :: get_path(SYS_TPL_PATH);
    }

    function get_cache_path()
    {
        return Path :: get_cache_path() . 'layout/' . Theme :: get_theme() . '/';
    }
}
?>