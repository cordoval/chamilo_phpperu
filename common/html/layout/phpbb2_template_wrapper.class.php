<?php
/***************************************************************************
 *                              template.php
 *                            -------------------
 *   begin                : Saturday, Feb 13, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: my_template.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

/**
 * Template class. By Nathan Codding of the phpBB group.
 * The interface was originally inspired by PHPLib templates,
 * and the template file formats are quite similar.
 *
 */

require_once(dirname(__FILE__) . '/template_cache.class.php');
require_once(Path :: get_plugin_path() . 'phpbb2/phpbb2_template.class.php');

class Phpbb2TemplateWrapper extends Phpbb2Template
{
    // The caching engine
    var $cache;

    /**
     * Constructor. Simply sets the root dir.
     *
     */
    function Phpbb2TemplateWrapper($theme)
    {
    	parent :: Template(Path :: get(SYS_PATH) .  'layout/' . $theme . '/templates/');
        $this->cache = TemplateCache :: factory($theme, 'file');
    }

    function set_theme($theme)
    {
        $this->root = Path :: get(SYS_PATH) .  'layout/' . $theme . '/templates/';
        $this->cache = TemplateCache :: factory($theme, 'file');
    }

    function reset()
    {
    }

    /**
     * Load the file for the handle, compile the file,
     * and run the compiled code.
     */
    function pparse($handle, $return = true)
    {
        if (! $this->loadfile($handle))
        {
            die("Template->pparse(): Couldn't load template file for handle $handle");
        }

        // actually compile the template now.
        if (!isset($this->compiled_code[$handle]) || empty($this->compiled_code[$handle]));
        {
			$this->compiled_code[$handle] = $this->cache->retrieve_from_cache($handle, $this->uncompiled_code[$handle]);

			if(!$this->compiled_code[$handle])
			{
	        	// Actually compile the code now.
	        	if($return)
	        	{
	        		$str = '';
	        		$this->compiled_code[$handle] = $this->compile($this->uncompiled_code[$handle], true, 'str');
	        	}
	        	else
	        	{
	        		$this->compiled_code[$handle] = $this->compile($this->uncompiled_code[$handle]);
	        	}

	        	$this->cache->cache($handle, $this->uncompiled_code[$handle], $this->compiled_code[$handle]);
			}
        }
        //echo '<pre>' . $this->compiled_code[$handle]; exit;
        // Run the compiled code.
        eval($this->compiled_code[$handle]);

        if($return)
        {
        	return $str;
        }
    }

    /**
     * Inserts the uncompiled code for $handle as the
     * value of $varname in the root-level. This can be used
     * to effectively include a template in the middle of another
     * template.
     * Note that all desired assignments to the variables in $handle should be done
     * BEFORE calling this function.
     */
    function assign_var_from_handle($varname, $handle)
    {
        if (! $this->loadfile($handle))
        {
            die("Template->assign_var_from_handle(): Couldn't load template file for handle $handle");
        }

        $_str = "";
        $this->compiled_code[$handle] = $this->cache->retrieve_from_cache($handle, $this->uncompiled_code[$handle]);

		if(!$this->compiled_code[$handle])
		{
	        // Compile it, with the "no echo statements" option on.
	        $code = $this->compile($this->uncompiled_code[$handle], true, '_str');
	        $this->cache->cache($handle, $this->uncompiled_code[$handle], $code);
		}

        // evaluate the variable assignment.
        eval($code);
        // assign the value of the generated variable to the given varname.
        $this->assign_var($varname, $_str);

        return true;
    }

    function add_filename($filename)
    {
    	$this->set_filenames(array(substr($filename, 0, -4) => $filename));
    }

    function render($handle, $return = true)
    {
        $result = $this->pparse($handle, $return);

        if ($return)
        {
            return $result;
        }
    }
}
?>