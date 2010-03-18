<?php
/**
 * $Id: pclzip_filecompression.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.filecompression.pclzip
 */
require_once Path :: get_plugin_path() . 'pclzip/pclzip.lib.php';
/**
 * This class implements file compression and extraction using the PclZip
 * library
 */
class PclzipFilecompression extends Filecompression
{

    function get_supported_mimetypes()
    {
        return array('application/x-zip-compressed', 'application/zip', 'multipart/x-zip', 'application/x-gzip', 'multipart/x-gzip');
    }

    function is_supported_mimetype($mimetype)
    {
        return in_array($mimetype, $this->get_supported_mimetypes());
    }

    function extract_file($file)
    {
        $dir = $this->create_temporary_directory();
        $pclzip = new PclZip($file);
        if ($pclzip->extract(PCLZIP_OPT_PATH, $dir) == 0)
        { 
        	dump($pclzip->errorInfo());
            return false;
        } 
        Filesystem :: create_safe_names($dir);
        return $dir;
    }

    function create_archive($path)
    {
        $archive_file = $this->get_filename();

        if (! isset($archive_file))
        {
            $archive_file = Filesystem :: create_unique_name($this->get_path(SYS_TEMP_PATH), uniqid() . '.zip');
        }

        $archive_file = $this->get_path(SYS_TEMP_PATH) . uniqid() . '_' . $archive_file;
        $content = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES, true);

        $pclzip = new PclZip($archive_file);
        $path_to_remove = realpath($path);
        $pclzip->add($content, PCLZIP_OPT_REMOVE_PATH, $path_to_remove);
        return $archive_file;
    }
}
?>