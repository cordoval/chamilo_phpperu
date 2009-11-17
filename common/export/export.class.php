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
    private $filename;

    /**
     * Constructor
     * @param string $filename
     */
    public function Export($filename)
    {
        $this->filename = $filename;
        Export :: get_supported_filetypes();
    }

    /**
     * Gets the filename
     * @return string
     */
    protected function get_filename()
    {
        return $this->filename;
    }

    /**
     * Writes the given data to a file
     * @param array $data
     */
    abstract function write_to_file($data);

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
            $type = basename($directory);
            if ($type != '.svn')
            {
                if (! in_array($type, $exclude))
                    $types[$type] = $type;
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
    public static function factory($type, $filename = 'export')
    {
        $file = dirname(__FILE__) . '/' . $type . '/' . $type . '_export.class.php';
        $class = Utilities :: underscores_to_camelcase($type) . 'Export';
        if (file_exists($file))
        {
            require_once ($file);
            return new $class($filename . '.' . $type);
        }
    }

    protected function get_path($path_type)
    {
        return Path :: get($path_type);
    }
}
?>