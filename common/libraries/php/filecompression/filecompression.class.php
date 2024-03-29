<?php
namespace common\libraries;
/**
 * $Id: filecompression.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.filecompression
 */
/**
 * An abstract class for handling file compression. Impement new compression
 * methods by creating a class which extends this abstract class.
 */
abstract class Filecompression
{
    private $filename;

    /**
     * Constructor
     */
    function __construct()
    {
    }

    /**
     * Creates a temporary directory in which the file can be extracted
     * @return string The full path to the created directory
     * @todo Put this function in filesystem class
     */
    protected function create_temporary_directory()
    {
        $path = $this->get_path(SYS_TEMP_PATH) . uniqid();
        Filesystem :: create_dir($path);
        return $path;
    }

    /**
     * Retrieves an array of all supported mimetypes for this file compression
     * implementation.
     * @return array
     */
    abstract function get_supported_mimetypes();

    /**
     * Determines if a given mimetype is supported by the file compression
     * implementation.
     * @return boolean True if the given mimetype is supported.
     */
    abstract function is_supported_mimetype($mimetype);

    /**
     * Extracts a compressed file to a given directory. This function will also
     * make sure that all resulting directory- and filenames are safe using the
     * Filesystem::create_safe_names function.
     * @see Filesystem::create_safe_names
     * @param string $file The full path to the file which should be extracted
     * @return string|boolean The full path to the directory where the file was
     * extracted or boolean false if extraction wasn't successfull
     */
    abstract function extract_file($file);

    /**
     * Creates an archive containing all contents from the given directory.
     * @param string $path The full path to the content that should be stored in
     * the archive.
     * @return string The full path to the created archive file.
     */
    abstract function create_archive($path);

    /**
     * Create a filecompression instance
     * @todo At the moment this returns the class using pclzip. The class to
     * return should be configurable
     */
    public static function factory()
    {
        require_once dirname(__FILE__) . '/pclzip/pclzip_filecompression.class.php';
        return new PclzipFilecompression();
    }

    protected function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    function set_filename($filename, $file_extension = 'cpo')
    {
        $this->filename = Filesystem :: create_safe_name($filename) . '.' . $file_extension;
    }

    function get_filename()
    {
        return $this->filename;
    }
}
?>