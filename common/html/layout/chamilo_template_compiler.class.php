<?php
require_once Path :: get_plugin_path() . 'phpbb3/functions_template.php';

class ChamiloTemplateCompiler extends template_compile
{

    /**
     * Load template source from file
     * @access private
     */
    function _tpl_load_file($handle)
    {
        // Try and open template for read
        if (! file_exists($this->template->files[$handle]))
        {
            trigger_error("template->_tpl_load_file(): File {$this->template->files[$handle]} does not exist or is empty", E_USER_ERROR);
        }

        $this->template->compiled_code[$handle] = $this->compile(trim(@file_get_contents($this->template->files[$handle])));

        // Actually compile the code now.
        $this->compile_write($handle, $this->template->compiled_code[$handle]);
    }

    /**
     * Write compiled file to cache directory
     * @access private
     */
    function compile_write($handle, $data)
    {
        $cache_path = $this->template->cachepath;

        if (! file_exists($cache_path))
        {
            Filesystem :: create_dir($cache_path, '0744');
        }

        $filename = $cache_path . str_replace('/', '.', $this->template->filename[$handle]) . '.php';

        if ($fp = fopen($filename, 'wb'))
        {
            flock($fp, LOCK_EX);
            fwrite($fp, $data);
            flock($fp, LOCK_UN);
            fclose($fp);

            Filesystem :: chmod($filename, '0744');
        }

        return;
    }
}

?>