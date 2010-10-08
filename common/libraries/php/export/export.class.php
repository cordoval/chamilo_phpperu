<?php
/**
 * $Id: export.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.export
 */
/**
 * Abstract class to export tabular data.
 * Create a new type of export by extending this class.
 */
abstract class Export
{
    /**
     * The filename which will be used for the export file.
     */
    private $data;
    private $filename;
    private $path;

    /**
     * Constructor
     * @param string $filename
     */
    public function Export($data)
    {
        $this->data = $data;
        Export :: get_supported_filetypes();
    }

    /**
     * Gets the data
     * @return string
     */
    protected function get_data()
    {
        return $this->data;
    }

    function get_filename()
    {
        return $this->filename;
    }

    function set_filename($filename)
    {
        $this->filename = $filename . '.' . $this->get_type();
    }

    function get_path()
    {
        if ($this->path)
        {
            return $this->path;
        }
        else
        {
            return Path :: get(SYS_ARCHIVE_PATH);
        }
    }

    function set_path($path)
    {
        $this->path = $path;
    }

    abstract function get_type();

    /**
     * Writes the given data to a file
     * @param array $data
     */
    public function write_to_file()
    {
        $file = $this->get_path() . Filesystem :: create_unique_name($this->get_path(), $this->get_filename());
        $handle = fopen($file, 'a+');
        if (! fwrite($handle, $this->render_data()))
        {
            return false;
        }
        fclose($handle);
        return $file;
    }

    public function send_to_browser()
    {
        $file = $this->write_to_file();
        if ($file)
        {
            Filesystem :: file_send_for_download($file, true, $this->get_filename());
            exit();
        }
    }

    abstract function render_data();

    /**
     * Gets the supported filetypes for export
     * @return array Array containig all supported filetypes (keys and values
     * are the same)
     */
    public static function get_supported_filetypes($exclude = array())
    {
        $directories = Filesystem :: get_directory_content(dirname(__FILE__), Filesystem :: LIST_DIRECTORIES, false);
        foreach ($directories as $index => $directory)
        {
           if ($directory != 'layout')
            {
                $type = basename($directory);
                if ($type != '.svn')
                {
                    if (! in_array($type, $exclude))
                        $types[$type] = $type;
                }
            }
        }
        return $types;
    }

    /**
     * Factory function to create an instance of an export class
     * @param string $type One of the supported file types returned by the
     * get_supported_filetypes function.
     * @param string $filename The desired filename for the export file
     * (extension will be automatically added depending on the given $type)
     */
    public static function factory($type, $data)
    {
        $file = dirname(__FILE__) . '/' . $type . '/' . $type . '_export.class.php';
        $class = Utilities :: underscores_to_camelcase($type) . 'Export';
        if (file_exists($file))
        {
            require_once ($file);
            return new $class($data);
        }
    }
}
?>