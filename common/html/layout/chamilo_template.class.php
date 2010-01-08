<?php
require_once Path :: get_plugin_path() . 'phpbb3/phpbb3_template.class.php';

class ChamiloTemplate extends template
{
    function __construct()
    {
        $this->set_template();
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
        $this->root = Theme :: get_template_path();
        $this->cachepath = Path :: get_temp_path() . 'cache/tpl_' . Theme :: get_theme() . '/';

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
                trigger_error("template->set_filenames: Empty filename specified for $handle", E_USER_ERROR);
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
        $user = $this->get_user();

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
        $user = $this->get_user();
        $config = $this->get_configuration();

        if (! isset($this->filename[$handle]))
        {
            trigger_error("template->_tpl_load(): No file specified for handle $handle", E_USER_ERROR);
        }

        $filename = $this->cachepath . str_replace('/', '.', $this->filename[$handle]) . '.php';

        $recompile = false;
        if (! file_exists($filename) || @filesize($filename) === 0)
        {
            $recompile = true;
        }
        else
            if ($config['load_tplcompile'])
            {
                $recompile = (@filemtime($filename) < filemtime($this->files[$handle])) ? true : false;
            }

        // Recompile page if the original template is newer, otherwise load the compiled version
        if (! $recompile)
        {
            return $filename;
        }

        if (! class_exists('template_compile'))
        {
            include Path :: get_plugin_path() . 'phpbb3/includes/functions_template.php';
        }

        $compile = new template_compile($this);

        // If we don't have a file assigned to this handle, die.
        if (! isset($this->files[$handle]))
        {
            trigger_error("template->_tpl_load(): No file specified for handle $handle", E_USER_ERROR);
        }

        // Just compile if no user object is present (happens within the installer)
        if (! $user)
        {
            $compile->_tpl_load_file($handle);
            return false;
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
			$user = $this->get_user();

			if ($filename)
			{
				include($filename);
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

		if (!file_exists($file))
		{
			// trigger_error cannot be used here, as the output already started
			echo 'template->_php_include(): File ' . htmlspecialchars($file) . ' does not exist or is empty';
			return;
		}
		include($file);
	}
}
?>