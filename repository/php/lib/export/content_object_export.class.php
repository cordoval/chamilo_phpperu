<?php
/**
 * $Id: content_object_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export
 */
/**
 * Abstract class to export tabular data.
 * Create a new type of export by extending this class.
 */
abstract class ContentObjectExport
{
    /**
     * The learning object to be exported.
     */
    private $content_object;

    /**
     * Constructor
     * @param string $content_object
     */
    public function ContentObjectExport($content_object)
    {
        $this->content_object = $content_object;
    }

    /**
     * Gets the learning object
     * @return ContentObject
     */
    protected function get_content_object()
    {
        return $this->content_object;
    }

    /**
     * Gets the supported filetypes for export
     * @return array Array containig all supported filetypes (keys and values
     * are the same)
     */
    public static function get_supported_filetypes()
    {
        $directories = Filesystem :: get_directory_content(dirname(__FILE__), Filesystem :: LIST_DIRECTORIES, false);
        foreach ($directories as $index => $directory)
        {
            $type = basename($directory);
            if ($type != '.svn')
            {
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
    public static function factory($type, $content_object)
    {
        $file = dirname(__FILE__) . '/' . $type . '/' . $type . '_export.class.php';
        $class = Utilities :: underscores_to_camelcase($type) . 'Export';
        if (file_exists($file))
        {
            require_once ($file);
            return new $class($content_object);
        }
    }

    protected function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    abstract function export_content_object();
}
?>