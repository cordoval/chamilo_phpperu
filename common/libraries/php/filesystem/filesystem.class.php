<?php
/**
 * $Id: filesystem.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.filesystem
 */
/**
 * This class implements some usefull functions to hanlde the filesystem.
 * @todo Implement other usefull functions which are now in files like
 * fileManage.lib.php, document.lib.php, fileUpload.lib.php But keep the
 * functions to filesystem-related stuff. So this isn't the place for code for
 * getting an icon to match a documents filetype for example.
 * @todo Make sure all functions in this class remove special chars before doing
 * stuff. So other modules shouldn't take care of the special chars problems.
 * This also means some functions which now return boolean should return the
 * changed pathname or filename after they successfully finished their
 * work.
 */
class Filesystem
{
    /**
     * Constant representing "Files and directories"
     */
    const LIST_FILES_AND_DIRECTORIES = 1;
    /**
     * Constant representing "Files"
     */
    const LIST_FILES = 2;
    /**
     * Constant representing "Directories"
     */
    const LIST_DIRECTORIES = 3;

    /**
     * Creates a directory.
     * This function creates all missing directories in a given path.
     * @param string $path
     * @param string $mode
     * @return boolean True if successfull, false if not.
     */
    public static function create_dir($path, $mode = null)
    {
    	if (! $mode)
        {
            $mode = 0777;
            /*if(class_exists('PlatformSetting'))
			{
				$ad = PlatformSetting :: get('permissions_new_directories');
				if($ad && $ad != '')
					$mode = $ad;
			}*/
        
        }
        // If the given path is a file, return false
        if (is_file($path))
        {
            return false;
        }
        // If the directory doesn't exist yet, create it using php's mkdir function
        if (! is_dir($path))
        {
        	if (! mkdir($path, $mode, true))
            {
            	return false;
            }
            else
            {
            	return chmod($path, $mode);
            }
        }
        return true;
    }

    /**
     * Copies a file. If the destination directory doesn't exist, this function
     * tries to create the directory using the Filesystem::create_dir function.
     * @param string $source The full path to the source file
     * @param string $destination The full path to the destination file
     * @param boolean $overwrite If the destination file allready exists, should
     * it be overwritten?
     * @return boolean True if successfull, false if not.
     */
    public static function copy_file($source, $destination, $overwrite = false)
    {
        if (file_exists($destination) && ! $overwrite)
        {
            return false;
        }
        $destination_dir = dirname($destination);
        if (file_exists($source) && Filesystem :: create_dir($destination_dir))
        {
            return copy($source, $destination);
        }
    }

    /**
     * Made a recursive copy function to copy entire directories
     *
     * @param String $source
     * @param String $destination
     * @param Bool $overwrite
     * @return Bool succes
     */
    function recurse_copy($source, $destination, $overwrite = false)
    {
        if (! is_dir($source))
            return self :: copy_file($source, $destination, $overwrite);
        $bool = true;
        
        $content = self :: get_directory_content($source, self :: LIST_FILES_AND_DIRECTORIES, false);
        foreach ($content as $file)
        {
            $path_to_file = $source . '/' . $file;
            $path_to_new_file = $destination . '/' . $file;
            if (! is_dir($path_to_file))
            {
                $bool &= self :: copy_file($path_to_file, $path_to_new_file, $overwrite);
            }
            else
            {
                self :: create_dir($path_to_new_file);
                $bool &= self :: recurse_copy($path_to_file, $path_to_new_file, $overwrite);
            }
        }
        
        return $bool;
    }
	
    function recurse_move($source, $destination, $overwrite = false)
    {
        if (! is_dir($source))
            return self :: move_file($source, $destination, $overwrite);
        $bool = true;
        
        $content = self :: get_directory_content($source, self :: LIST_FILES_AND_DIRECTORIES, false);
        foreach ($content as $file)
        {
            $path_to_file = $source . '/' . $file;
            $path_to_new_file = $destination . '/' . $file;
            if (! is_dir($path_to_file))
            {
                $bool &= self :: move_file($path_to_file, $path_to_new_file, $overwrite);
            }
            else
            {
                self :: create_dir($path_to_new_file);
                $bool &= self :: recurse_move($path_to_file, $path_to_new_file, $overwrite);
            }
        }
        
        return $bool;
    }
    
    /**
     * Moves a file. If the destination directory doesn't exist, this function
     * tries to create the directory using the Filesystem::create_dir function.
     * Path cannot have a '/' at the end 
     * @param string $source The full path to the source file
     * @param string $destination The full path to the destination file
     * @param boolean $overwrite If the destination file allready exists, should
     * it be overwritten?
     * @return boolean True if successfull, false if not.
     */
    public static function move_file($source, $destination, $overwrite = false)
    {
        if (file_exists($destination) && ! $overwrite)
        {
        	return false;
        }
        $destination_dir = dirname($destination);
        if (file_exists($source) && Filesystem :: create_dir($destination_dir))
        {
        	return rename($source, $destination);
        }
    }
    /**
     * Creates a unique name for a file or a directory. This function will also
     * use the function Filesystem::create_safe_name to make sure the resulting
     * name is safe to use.
     * @param string $desired_path The path
     * @param string $desired_filename The desired filename
     * @return string A unique name based on the given desired_name
     */
    public static function create_unique_name($desired_path, $desired_filename = null)
    {
        $index = 0;
        if (! is_null($desired_filename))
        {
            $filename = Filesystem :: create_safe_name($desired_filename);
            $new_filename = $filename;
            while (file_exists($desired_path . '/' . $new_filename))
            {
                $file_parts = explode('.', $filename);
                if (count($file_parts) > 1)
                    $new_filename = array_shift($file_parts) . ($index ++) . '.' . implode('.', $file_parts);
                else
                    $new_filename = array_shift($file_parts) . ($index ++);
            }
            return $new_filename;
        }
        $unique_path = dirname($desired_path) . '/' . Filesystem :: create_safe_name(basename($desired_path));
        while (is_dir($unique_path))
        {
            $unique_path = $desired_path . ($index ++);
        }
        return $unique_path;
    }

    /**
     * Creates a safe name for a file or directory
     * @param string $desired_name The desired name
     * @return string The safe name
     */
    public static function create_safe_name($desired_name)
    {
        //Change encoding
        $safe_name = mb_convert_encoding($desired_name, "ISO-8859-1", "UTF-8");
        //Replace .php by .phps
        //$safe_name = eregi_replace("\.(php.?|phtml)$", ".phps", $safe_name);
        //If first letter is . add something before
        $safe_name = preg_replace("/^\./", "0.", $safe_name);
        //Replace accented characters
        $safe_name = strtr($safe_name, 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïðñòóôõöøùúûüýÿ', 'aaaaaaaceeeeiiiidnoooooouuuuyaaaaaaceeeeiiiidnoooooouuuuyy');
        //Replace all except letters, numbers, - and . to underscores
        $safe_name = preg_replace('[^0-9a-zA-Z\-\.]', '_', $safe_name);
        //Replace set of underscores by a single underscore
        $safe_name = preg_replace('/[_]+/', '_', $safe_name);
        return $safe_name;
    }

    /**
     * Scans all files and directories in the given path and subdirectories. If
     * a file or directory name isn't considered as safe, it will be renamed to
     * a safe name.
     * @param string $path The full path to the directory. This directory will
     * not be renamed, only its content.
     */
    public static function create_safe_names($path)
    {
        $list = Filesystem :: get_directory_content($path);
        // Sort everything, so renaming a file or directory has no impact on next elements in the array
        rsort($list);
        foreach ($list as $index => $entry)
        {
            if (basename($entry) != Filesystem :: create_safe_name(basename($entry)))
            {
                if (is_file($entry))
                {
                    $safe_name = Filesystem :: create_unique_name(dirname($entry), basename($entry));
                    $destination = dirname($entry) . '/' . $safe_name;
                    Filesystem :: copy_file($entry, $destination);
                    unlink($entry);
                }
                elseif (is_dir($entry))
                {
                    $safe_name = Filesystem :: create_unique_name($entry);
                    rename($entry, $safe_name);
                }
            }
        }
    }

    /**
     * Writes content to a file. This function will try to create the path and
     * the file if they don't exist yet.
     * @param string $file The full path to the file
     * @param string $content
     * @param boolean $append If true the given conten will be appended to the
     * end of the file
     */
    public static function write_to_file($file, $content, $append = false)
    {
        if (Filesystem :: create_dir(dirname($file)))
        {
            if ($create_file = fopen($file, $append ? 'a' : 'w'))
            {
                fwrite($create_file, $content);
                fclose($create_file);
                chmod($file, 0777);
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Determines the number of bytes taken by a given directory or file
     * @param string $path The full path to the file or directory of which the
     * disk space should be determined
     * @return int The number of bytes taken on disk by the given directory or
     * file
     */
    public static function get_disk_space($path)
    {
        if (is_file($path))
        {
            return filesize($path);
        }
        if (is_dir($path))
        {
            $total_disk_space = 0;
            $files = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES);
            foreach ($files as $index => $file)
            {
                $total_disk_space += @filesize($file);
            }
            return $total_disk_space;
        }
        // If path doesn't exist, return null
        return 0;
    }

    /**
     * Guesses the disk space used when the given content would be written to a
     * file
     * @param string $content
     * @return int The number of bytes taken on disk by a file containing the
     * given content
     */
    public static function guess_disk_space($content)
    {
        $tmpfname = tempnam();
        $handle = fopen($tmpfname, "w");
        fwrite($handle, $content);
        fclose($handle);
        $disk_space = Filesystem :: get_disk_space($tmpfname);
        unlink($tmpfname);
        return $disk_space;
    }

    /**
     * Retrieves all contents (files and/or directories) of a directory
     * @param string $path The full path of the directory
     * @param const $type Type to determines which items should be included in
     * the resulting list
     * @param boolean $recursive If true, all content of all subdirectories will
     * also be returned.
     * @return array Containing the requested directory contents. All entries
     * are full paths.
     */
    public static function get_directory_content($path, $type = Filesystem::LIST_FILES_AND_DIRECTORIES, $recursive = true)
    {
        $result = array();
        
    	if ($recursive)
        {
            $it = new RecursiveDirectoryIterator($path);
            $it = new RecursiveIteratorIterator($it, 1);
        }
        else
        {
            $it = new DirectoryIterator($path);
        }
        foreach ($it as $entry)
        {
            if ($it->isDot())
            {
                continue;
            }
            if (($type == Filesystem :: LIST_FILES_AND_DIRECTORIES || $type == Filesystem :: LIST_FILES) && $entry->isFile())
            {
                //getRealPath() results in php-error in older PHP5 versions
                //$result[] = $entry->getRealPath();
                $result[] = $entry->__toString();
            }
            if (($type == Filesystem :: LIST_FILES_AND_DIRECTORIES || $type == Filesystem :: LIST_DIRECTORIES) && $entry->isDir())
            {
                //getRealPath() results in php-error in older PHP5 versions
                //$result[] = $entry->getRealPath();
                $result[] = $entry->__toString();
            }
        }
        
        return $result;
    }

    /**
     * Removes a file or a directory (and all its contents).
     * @param string $path To full path to the file or directory to delete
     * @return boolean True if successfull, false if not. When a directory is
     * given to delete, this function will delete as much as possible from this
     * directory. If some subdirectories or files in the given directory can't
     * be deleted, this function will return false.
     */
    public static function remove($path)
    {
        if(realpath($path) == '/')
        	return false;
        	
    	if (is_file($path))
        {
            return @unlink($path);
        }
        elseif (is_dir($path))
        {
        	$content = Filesystem :: get_directory_content($path);
            // Reverse sort the content so deepest entries come first.
            rsort($content);
            $result = true;
            foreach ($content as $index => $entry)
            {
                if (is_file($entry))
                {
                    $result &= @unlink($entry);
                }
                elseif (is_dir($entry))
                {
                    $result &= @rmdir($entry);
                }
            }
            return ($result & @rmdir($path));
        }
    }

	/**
     * Copy a file from one directory to another directory, but with protection to rename
     * files when there is already a file in the destination directory with the same name but
     * a different md5 hash
     * @param string $source_path full path to source directory
     * @param string $source_filename name of first file
     * @param string $destination_path full path to second file
     * @param string $destination_filename name of second file
     * @return A new unique name when changes was needed, otherwise null
     */
    public static function copy_file_with_double_files_protection($source_path, $source_filename, $destination_path, $destination_filename, $move_file)
    {
        $source_file = $source_path . $source_filename;
        $destination_file = $destination_path . $destination_filename;

        if (! file_exists($source_file) || ! is_file($source_file))
        {
            return null;
        }
        
        if (file_exists($destination_file) && is_file($destination_file))
        {
            if (! (md5_file($source_file) == md5_file($destination_file)))
            {
                $new_unique_file = self :: create_unique_name($destination_path, $destination_filename);
                 
                if ($move_file)
                {
                	self :: move_file($source_file, $destination_path . $new_unique_file);
                }
                else
                {
                    self :: copy_file($source_file, $destination_path . $new_unique_file);
                }
                
                return $new_unique_file;
            }
            else
            {
                return $destination_filename;
            }
        }
        
        if ($move_file)
        {
            self :: move_file($source_file, $destination_file);
        }
        else
        {
            self :: copy_file($source_file, $destination_file);
        }
        
        return $destination_filename;
    }
    
    /**
     * Transform the file size in a human readable format.
     *
     * @param  - $file_size (int) - Size of the file in bytes
     * @return - A human readable representation of the file size
     */
    public static function format_file_size($file_size)
    {
        // Todo: Megabyte vs Mebibyte...
        $kilobyte = 1024;
        $megabyte = pow($kilobyte, 2);
        $gigabyte = pow($kilobyte, 3);
        if ($file_size >= $gigabyte)
        {
            $file_size = round($file_size / $gigabyte * 100) / 100 . 'GB';
        }
        elseif ($file_size >= $megabyte)
        {
            $file_size = round($file_size / $megabyte * 100) / 100 . 'MB';
        }
        elseif ($file_size >= $kilobyte)
        {
            $file_size = round($file_size / $kilobyte * 100) / 100 . 'kB';
        }
        else
        {
            $file_size = $file_size . 'B';
        }
        return $file_size;
    }

    /**
     * This function streams a file to the client
     *
     * @param string $full_file_name
     * @param boolean $forced
     * @param string $name
     * @return false if file doesn't exist, true if stream succeeded
     */
    public static function file_send_for_download($full_file_name, $forced = false, $name = '')
    {
        if (! is_file($full_file_name))
        {
            return false;
        }
        $filename = ($name == '') ? basename($full_file_name) : $name;
        $len = filesize($full_file_name);
        
        if ($forced)
        {
            //force the browser to save the file instead of opening it
            

            header('Content-type: application/octet-stream');
            //header('Content-Type: application/force-download');
            header('Content-length: ' . $len);
            if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
            {
                header('Content-Disposition: filename= ' . $filename);
            }
            else
            {
                header('Content-Disposition: attachment; filename= ' . $filename);
            }
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
            {
                header('Pragma: ');
                header('Cache-Control: ');
                header('Cache-Control: public'); // IE cannot download from sessions without a cache
            }
            header('Content-Description: ' . $filename);
            header('Content-transfer-encoding: binary');
            
            $fp = fopen($full_file_name, 'r');
            fpassthru($fp);
            return true;
        }
        else
        {
            //no forced download, just let the browser decide what to do according to the mimetype
            

            $content_type = DocumentManager :: file_get_mime_type($filename);
            header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Content-type: ' . $content_type);
            header('Content-Length: ' . $len);
            $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            if (strpos($user_agent, 'msie'))
            {
                header('Content-Disposition: ; filename= ' . $filename);
            }
            else
            {
                header('Content-Disposition: inline; filename= ' . $filename);
            }
            readfile($full_file_name);
            return true;
        }
    }

    /**
     * Call the chmod function on the given file path.
     * The chmod value must be the octal value, with or without its leading zero
     * 
     * Ex:
     * 		Filesystem :: chmod('/path/to/file', '666')		OK
     * 		Filesystem :: chmod('/path/to/file', '0666')	OK
     * 		Filesystem :: chmod('/path/to/file', 666)		OK
     * 		Filesystem :: chmod('/path/to/file', 0666)		OK
     * 
     * Note:
     * 		This function was written to facilitate the storage of a chmod value. 
     * 		
     * 		The PHP chmod value must be called with an octal number, but it is not easy 
     * 		to store a value with a leading 0 that is a number and not a string.
     * 
     * 
     * @param $file_path string Path to file or folder
     * @param $chmod_value mixed The chmod value as a string or an integer
     * @return void
     */
    public static function chmod($file_path, $chmod_value)
    {
        $new_chmod_value = null;
        
        if (is_integer($chmod_value))
        {
            $new_chmod_value = (int) $chmod_value;
        }
        elseif (is_string($chmod_value))
        {
            $new_chmod_value = intval($chmod_value);
        }
        
        if (isset($new_chmod_value) && file_exists($file_path))
        {
            $new_chmod_value = octdec($new_chmod_value);
            
            chmod($file_path, $new_chmod_value);
        }
    }

}
?>