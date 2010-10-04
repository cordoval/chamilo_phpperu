<?php
/**
 * $Id: document.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.document
 */
/**
 * A Document.
 */
class Document extends ContentObject implements Versionable
{
    const PROPERTY_PATH = 'path';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_FILESIZE = 'filesize';
    const PROPERTY_HASH = 'hash';

    const TYPE_IMAGE = 'image';
    const TYPE_FLASH = 'flash';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    /**
    * In memory file content. Will be saved on disk if it doesn't exist yet. Mainly used to create a new Document.
    *
    * @var mixed
    */
    private $in_memory_file;

    /**
    * Temporary file path. A path to a file that has to be moved and renamed when the Document is saved.
    * Useful for instance when a file is uploaded to the server.
    *
    * @var string
    */
    private $temporary_file_path;

    /**
    * Indicates wether the Document must be saved as a new version when its save() or update() method is called
    *
    * @var boolean
    */
    private $save_as_new_version = false;

    function get_path()
    {
        return $this->get_additional_property(self :: PROPERTY_PATH);
    }

    function set_path($path)
    {
        return $this->set_additional_property(self :: PROPERTY_PATH, $path);
    }

    function get_filename()
    {
        return $this->get_additional_property(self :: PROPERTY_FILENAME);
    }

    function set_filename($filename)
    {
        return $this->set_additional_property(self :: PROPERTY_FILENAME, $filename);
    }

    function get_filesize()
    {
        return $this->get_additional_property(self :: PROPERTY_FILESIZE);
    }

    function set_filesize($filesize)
    {
        return $this->set_additional_property(self :: PROPERTY_FILESIZE, $filesize);
    }

    function get_hash()
    {
        return $this->get_additional_property(self :: PROPERTY_HASH);
    }

    function set_hash($hash)
    {
        return $this->set_additional_property(self :: PROPERTY_HASH, $hash);
    }

    function delete()
    {
        $path = Path :: get(SYS_REPO_PATH) . $this->get_path();
        Filesystem :: remove($path);
        parent :: delete();
    }

	function get_mime_type()
	{
		//all mime types in an array (from 1.6, this is the authorative source)
		//please keep this alphabetical if you add something to this list!!!
	    $mime_types=array(
	        "aac" => "audio/aac",
			"ai" => "application/postscript",
			"aif" => "audio/x-aiff",
    		"aifc" => "audio/x-aiff",
   			"aiff" => "audio/x-aiff",
		    "asf" => "video/x-ms-asf",
		    "asc" => "text/plain",
		    "au" => "audio/basic",
		    "avi" => "video/x-msvideo",
		    "bcpio" => "application/x-bcpio",
		    "bin" => "application/octet-stream",
		    "bmp" => "image/bmp",
		    "cdf" => "application/x-netcdf",
		    "class" => "application/octet-stream",
		    "cpio" => "application/x-cpio",
		    "cpt" => "application/mac-compactpro",
		    "csh" => "application/x-csh",
		    "css" => "text/css",
		    "dcr" => "application/x-director",
		    "dir" => "application/x-director",
		    "djv" => "image/vnd.djvu",
		    "djvu" => "image/vnd.djvu",
		    "dll" => "application/octet-stream",
		    "dmg" => "application/x-diskcopy",
		    "dms" => "application/octet-stream",
		    "doc" => "application/msword",
		    "dvi" => "application/x-dvi",
		    "dwg" => "application/vnd.dwg",
		    "dxf" => "application/vnd.dxf",
		    "dxr" => "application/x-director",
		    "eps" => "application/postscript",
		    "etx" => "text/x-setext",
		    "exe" => "application/octet-stream",
		    "ez" => "application/andrew-inset",
		    "gif" => "image/gif",
		    "gtar" => "application/x-gtar",
		    "gz" => "application/x-gzip",
		    "hdf" => "application/x-hdf",
		    "hqx" => "application/mac-binhex40",
		    "htm" => "text/html",
		    "html" => "text/html",
		    "ice" => "x-conference-xcooltalk",
		    "ief" => "image/ief",
		    "iges" => "model/iges",
		    "igs" => "model/iges",
		    "jar" => "application/java-archiver",
		    "jpe" => "image/jpeg",
		    "jpeg" => "image/jpeg",
		    "jpg" => "image/jpeg",
		    "js" => "application/x-javascript",
		    "kar" => "audio/midi",
		    "latex" => "application/x-latex",
		    "lha" => "application/octet-stream",
		    "lzh" => "application/octet-stream",
		    "m1a" => "audio/mpeg",
		    "m2a" => "audio/mpeg",
		    "m3u" => "audio/x-mpegurl",
	        "m4a" => "audio/x-m4a",
		    "man" => "application/x-troff-man",
		    "me" => "application/x-troff-me",
		    "mesh" => "model/mesh",
		    "mid" => "audio/midi",
		    "midi" => "audio/midi",
		    "mov" => "video/quicktime",
		    "movie" => "video/x-sgi-movie",
		    "mp2" => "audio/mpeg",
		    "mp3" => "audio/mpeg",
		    "mp4" => "video/mpeg4-generic",
		    "mpa" => "audio/mpeg",
		    "mpe" => "video/mpeg",
		    "mpeg" => "video/mpeg",
		    "mpg" => "video/mpeg",
		    "mpga" => "audio/mpeg",
		    "ms" => "application/x-troff-ms",
		    "msh" => "model/mesh",
		    "mxu" => "video/vnd.mpegurl",
		    "nc" => "application/x-netcdf",
		    "oda" => "application/oda",
	        "ogg" => "audio/ogg",
		    "pbm" => "image/x-portable-bitmap",
		    "pct" => "image/pict",
		    "pdb" => "chemical/x-pdb",
		    "pdf" => "application/pdf",
		    "pgm" => "image/x-portable-graymap",
		    "pgn" => "application/x-chess-pgn",
		    "pict" => "image/pict",
		    "png" => "image/png",
		    "pnm" => "image/x-portable-anymap",
		    "ppm" => "image/x-portable-pixmap",
		    "ppt" => "application/vnd.ms-powerpoint",
		    "pps" => "application/vnd.ms-powerpoint",
		    "ps" => "application/postscript",
		    "qt" => "video/quicktime",
		    "ra" => "audio/x-realaudio",
		    "ram" => "audio/x-pn-realaudio",
		    "rar" => "image/x-rar-compressed",
		    "ras" => "image/x-cmu-raster",
		    "rgb" => "image/x-rgb",
		    "rm" => "audio/x-pn-realaudio",
		    "roff" => "application/x-troff",
		    "rpm" => "audio/x-pn-realaudio-plugin",
		    "rtf" => "text/rtf",
		    "rtx" => "text/richtext",
		    "sgm" => "text/sgml",
		    "sgml" => "text/sgml",
		    "sh" => "application/x-sh",
		    "shar" => "application/x-shar",
		    "silo" => "model/mesh",
		    "sib" => "application/X-Sibelius-Score",
		    "sit" => "application/x-stuffit",
		    "skd" => "application/x-koan",
		    "skm" => "application/x-koan",
		    "skp" => "application/x-koan",
		    "skt" => "application/x-koan",
		    "smi" => "application/smil",
		    "smil" => "application/smil",
		    "snd" => "audio/basic",
		    "so" => "application/octet-stream",
		    "spl" => "application/x-futuresplash",
		    "src" => "application/x-wais-source",
		    "sv4cpio" => "application/x-sv4cpio",
		    "sv4crc" => "application/x-sv4crc",
		    "svf" => "application/vnd.svf",
		    "swf" => "application/x-shockwave-flash",
		    "sxc" => "application/vnd.sun.xml.calc",
		    "sxi" => "application/vnd.sun.xml.impress",
		    "sxw" => "application/vnd.sun.xml.writer",
		    "t" => "application/x-troff",
		    "tar" => "application/x-tar",
		    "tcl" => "application/x-tcl",
		    "tex" => "application/x-tex",
		    "texi" => "application/x-texinfo",
		    "texinfo" => "application/x-texinfo",
		    "tga" => "image/x-targa",
		    "tif" => "image/tif",
		    "tiff" => "image/tiff",
		    "tr" => "application/x-troff",
		    "tsv" => "text/tab-seperated-values",
		    "txt" => "text/plain",
		    "ustar" => "application/x-ustar",
		    "vcd" => "application/x-cdlink",
		    "vrml" => "model/vrml",
		    "wav" => "audio/x-wav",
		    "wbmp" => "image/vnd.wap.wbmp",
		    "wbxml" => "application/vnd.wap.wbxml",
		    "wml" => "text/vnd.wap.wml",
		    "wmlc" => "application/vnd.wap.wmlc",
		    "wmls" => "text/vnd.wap.wmlscript",
		    "wmlsc" => "application/vnd.wap.wmlscriptc",
		    "wma" => "video/x-ms-wma",
		    "wmv" => "audio/x-ms-wmv",
		    "wrl" => "model/vrml",
		    "xbm" => "image/x-xbitmap",
		    "xht" => "application/xhtml+xml",
		    "xhtml" => "application/xhtml+xml",
		    "xls" => "application/vnd.ms-excel",
		    "xml" => "text/xml",
		    "xpm" => "image/x-xpixmap",
		    "xsl" => "text/xml",
		    "xwd" => "image/x-windowdump",
		    "xyz" => "chemical/x-xyz",
		    "zip" => "application/zip",
			"xlsx" => "application/vnd.openxmlformats",
			"docx" => "application/vnd.openxmlformats",
			"pptx" => "application/vnd.openxmlformats"
		);

		$filename = $this->get_filename();

		//get the extension of the file
		$extension = explode('.', $filename);

		//$filename will be an array if a . was found
		if (is_array($extension))
		{
			$extension = (strtolower($extension[sizeof($extension) - 1]));
		}
		//file without extension
		else
		{
			$extension = 'empty';
		}

		//if the extension is found, return the content type
		if (isset ($mime_types[$extension]))
			return $mime_types[$extension];
		//else return octet-stream
		return "application/octet-stream";
	}

    function delete_version()
    {
        $path = Path :: get(SYS_REPO_PATH) . $this->get_path();
        if (RepositoryDataManager :: get_instance()->is_only_document_occurence($this->get_path()))
        {
            Filesystem :: remove($path);
        }
        parent :: delete_version();
    }

    function get_url()
    {
        return Path :: get(WEB_REPO_PATH) . $this->get_path();
    }

    function get_full_path()
    {
        //return realpath(Configuration :: get_instance()->get_parameter('general', 'upload_path').'/'.$this->get_path());
        return Path :: get(SYS_REPO_PATH) . $this->get_path();
    }

    function get_icon_name()
    {
        $filename = $this->get_filename();
        $parts = explode('.', $filename);
        $icon_name = $parts[count($parts) - 1];
        $icon_path = Theme :: get_common_image_system_path() . 'content_object/document_' . $icon_name . '.png';

        if (! file_exists($icon_path))
        {
            return 'document';
        }

        return 'document_' . $icon_name;
    }

    static function get_disk_space_properties()
    {
        return 'filesize';
    }

    function get_extension()
    {
        $filename = $this->get_filename();
        $parts = explode('.', $filename);
        return $parts[count($parts) - 1];
    }

    /**
    * Get In memory file content. Will be saved on disk if it doesn't exist yet. Mainly used to create a new Document.
    *
    * @return mixed
    */
    public function get_in_memory_file()
    {
    	return $this->in_memory_file;
    }

    /**
    * Set In memory file content. Will be saved on disk if it doesn't exist yet. Mainly used to create a new Document.
    *
    * @var $in_memory_file mixed
    * @return void
    */
    public function set_in_memory_file($in_memory_file)
    {
        if(StringUtilities :: has_value($in_memory_file))
        {
            if(StringUtilities :: has_value($this->get_temporary_file_path()))
            {
                throw new Exception('A Document can not have a temporary file path and in memory content');
            }

            $this->in_memory_file = $in_memory_file;
        }
    }

    /**
    * Get a value indicating wether the Document must be saved as a new version if its save() or update() method is called
    *
    * @return boolean
    */
    public function get_save_as_new_version()
    {
    	return $this->save_as_new_version;
    }

    /**
    * Set a value indicating wether the Document must be saved as a new version if its save() or update() method is called
    *
    * @var $save_as_new_version boolean
    * @return void
    */
    public function set_save_as_new_version($save_as_new_version)
    {
        if(is_bool($save_as_new_version))
        {
            $this->save_as_new_version = $save_as_new_version;
        }
    }

	/**
    * Get temporary file path. A path to a file that has to be moved and renamed when the Document is saved
    *
    * @return string
    */
    public function get_temporary_file_path()
    {
    	return $this->temporary_file_path;
    }

    /**
    * Set temporary file path. A path to a file that has to be moved and renamed when the Document is saved
    *
    * @var $temporary_file_path string
    * @return void
    */
    public function set_temporary_file_path($temporary_file_path)
    {
        if(StringUtilities :: has_value($temporary_file_path))
        {
            if(StringUtilities :: has_value($this->get_in_memory_file()))
            {
                throw new Exception('A Document can not have a temporary file path and in memory content');
            }

            $this->temporary_file_path = $temporary_file_path;
        }
    }

    public function has_file_to_save()
    {
        return StringUtilities :: has_value($this->get_temporary_file_path()) || StringUtilities :: has_value($this->get_in_memory_file());
    }

    /**
     * Determines if this document is an image
     * @return boolean True if the document is an image
     */
    function is_image()
    {
        $extension = $this->get_extension();
        return in_array($extension, $this->get_image_types());
    }

    /**
     * Determines if this document is a flash movie
     * @return boolean True if the document is a flash movie
     */
    function is_flash()
    {
        $extension = $this->get_extension();
        return in_array($extension, $this->get_flash_types());
    }

    /**
     * Determines if this document is a video
     * @return boolean True if the document is a video
     */
    function is_video()
    {
        $extension = $this->get_extension();
        return in_array($extension, $this->get_video_types());
    }

    /**
     * Determines if this document is an audio file
     * @return boolean True if the document is an audio file
     */
    function is_audio()
    {
        $extension = $this->get_extension();
        return in_array($extension, $this->get_audio_types());
    }

    static function get_image_types()
    {
        $image_types = array();
        $image_types[] = 'gif';
        $image_types[] = 'png';
        $image_types[] = 'jpg';
        $image_types[] = 'jpeg';
        $image_types[] = 'svg';
        $image_types[] = 'bmp';
        $image_types[] = 'GIF';
        $image_types[] = 'PNG';
        $image_types[] = 'JPG';
        $image_types[] = 'JPEG';
        $image_types[] = 'SVG';
        $image_types[] = 'BMP';

        return $image_types;
    }

    static function get_flash_types()
    {
        $flash_types = array();
        $flash_types[] = 'swf';
        $flash_types[] = 'SWF';

        return $flash_types;
    }

    static function get_video_types()
    {
        $video_types = array();
        $video_types[] = 'mpg';
        $video_types[] = 'mpeg';
        $video_types[] = 'mp4';
        $video_types[] = 'avi';
        $video_types[] = 'wmv';
        $video_types[] = 'mov';
        $video_types[] = '3gp';
        $video_types[] = 'flv';
        $video_types[] = 'mkv';
        $video_types[] = 'MPG';
        $video_types[] = 'MPEG';
        $video_types[] = 'MP4';
        $video_types[] = 'AVI';
        $video_types[] = 'WMV';
        $video_types[] = 'MOV';
        $video_types[] = '3GP';
        $video_types[] = 'FLV';
        $video_types[] = 'MKV';

        return $video_types;
    }

    static function get_audio_types()
    {
        $audio_types = array();
        $audio_types[] = 'mp3';
        $audio_types[] = 'wma';
        $audio_types[] = 'ogg';
        $audio_types[] = 'aac';
        $audio_types[] = 'm4a';
        $audio_types[] = 'midi';
        $audio_types[] = 'wav';
//        $audio_types[] = '';
        $audio_types[] = 'MP3';
        $audio_types[] = 'WMA';
        $audio_types[] = 'OGG';
        $audio_types[] = 'AAC';
        $audio_types[] = 'M4A';
        $audio_types[] = 'MIDI';
        $audio_types[] = 'WAV';
//        $audio_types[] = '';

//        $audio_types[] = '';

        return $audio_types;
    }

    function send_as_download()
    {
        $filename = str_replace(' ', '_', $this->get_filename());

    	header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Cache-Control: public');
        header('Pragma: no-cache');
        header('Content-type: ' . $this->get_mime_type());
        //header('Content-Type: application/force-download');
        header('Content-length: ' . $this->get_filesize());
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
        $fp = fopen($this->get_full_path(), 'r');
        fpassthru($fp);
        return true;
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_FILENAME, self :: PROPERTY_FILESIZE, self :: PROPERTY_PATH, self :: PROPERTY_HASH);
    }

    /**
     * (non-PHPdoc)
     * @see common/DataClass#check_before_save()
     */
    protected function check_before_save()
    {
        //Title
        if(StringUtilities :: is_null_or_empty($this->get_title()))
        {
            $this->add_error(Translation :: get('DocumentTitleIsRequired'));
        }

        //Description
        if(PlatformSetting :: get('description_required', 'repository') && StringUtilities :: is_null_or_empty($this->get_description()))
        {
            $this->add_error(Translation :: get('DocumentDescriptionIsRequired'));
        }

        //OwnerId
        $owner_id = $this->get_owner_id();
        if(!isset($owner_id) || !is_numeric($owner_id))
        {
            $this->add_error(Translation :: get('ContentObjectOwnerIsRequired'));
        }

        /*
         * Save file if needed
         */
        if($this->has_file_to_save())
        {
            $this->save_file();
        }
        else
        {
            /*
             * Make a copy of the current file if the update has to create a new version, without saving a new content
             */
            if($this->save_as_new_version && !$this->has_file_to_save())
            {
                if(!$this->duplicate_current_file())
                {
                    $this->add_error(Translation :: get('DocumentDuplicateError'));
                }
            }

            $fullpath = $this->get_full_path();

            if(!isset($fullpath) || !file_exists($fullpath))
            {
                $this->add_error(Translation :: get('DocumentFileContentNotSet'));
            }
        }

        //Filename
        if(StringUtilities :: is_null_or_empty($this->get_filename()))
        {
            $this->add_error(Translation :: get('DocumentFilenameIsRequired'));
        }

        //Path
        if(StringUtilities :: is_null_or_empty($this->get_path()))
        {
            $this->add_error(Translation :: get('DocumentPathToFileNotSet'));
        }

        //Hash
        if(StringUtilities :: is_null_or_empty($this->get_hash()))
        {
            $this->add_error(Translation :: get('DocumentHashNotSet'));
        }

        return !$this->has_errors();
    }

    /**
     * Save the in memory file or the temporary file to the current user disk space
     * Return true if the file could be saved
     *
     * @return boolean
     */
    private function save_file()
    {
        $save_success = false;

        if($this->has_file_to_save())
        {
            //DebugUtilities :: show($this->in_memory_file);

            $filename = $this->get_filename();
            if(isset($filename))
            {
                /*
                 * Delete current file before to create it again if the object is not saved as a new version
                 */
                $as_new_version = $this->get_save_as_new_version();
                if(!$as_new_version)
                {
                    $current_path = $this->get_path();

                    if(isset($current_path) && is_file(Path :: get(SYS_REPO_PATH) . $current_path))
                    {
                         Filesystem :: remove(Path :: get(SYS_REPO_PATH) . $current_path);
                         //DebugUtilities :: show('delete : ' . Path :: get(SYS_REPO_PATH) . $current_path);
                    }
                }

                $filename_hash        = md5($filename);
                $relative_folder_path = $this->get_owner_id() . '/' . Text :: char_at($filename_hash, 0);
                $full_folder_path     = Path :: get(SYS_REPO_PATH) . $relative_folder_path;

                Filesystem :: create_dir($full_folder_path);
                $unique_hash = Filesystem :: create_unique_name($full_folder_path, $filename_hash);

                $relative_path = $relative_folder_path . '/' . $unique_hash;
                $path_to_save  = $full_folder_path . '/' . $unique_hash;

                //DebugUtilities :: show($full_path);

                $save_success = false;
                if(StringUtilities :: has_value($this->temporary_file_path))
                {
                    if(Filesystem :: move_file($this->temporary_file_path, $path_to_save, !$as_new_version))
                    {
                    	$save_success = true;
                    }
                    else
                    {
                    	if(FileSystem :: copy_file($this->temporary_file_path, $path_to_save, !$as_new_version))
                    	{
                    		if(FileSystem :: remove($this->temporary_file_path))
                    		{
                    			$save_success = true;
                    		}
                    	}
                    }

                }
                elseif(StringUtilities :: has_value($this->in_memory_file) && Filesystem :: write_to_file($path_to_save, $this->in_memory_file))
                {
                    $save_success = true;
                }

                if($save_success)
                {
                    Filesystem :: chmod($path_to_save, PlatformSetting :: get('permissions_new_files'));

                    $file_bytes = Filesystem :: get_disk_space($path_to_save);

                    $this->set_filesize($file_bytes);
                    $this->set_path($relative_path);
                    $this->set_hash($unique_hash);
                    $this->set_content_hash(md5_file($path_to_save));
                }
                else
                {
                     $this->add_error(Translation :: get('DocumentStoreError'));
                }
            }
            else
            {
                $this->add_error(Translation :: get('DocumentFilenameNotSet'));
            }
        }

        return $save_success;
    }

    /**
     * Copy the current file to a new unique filename.
     * Set the new values of path and hash of the current object.
     *
     * Useful when a Document is updated as a new version, without replacing the content
     *
     * Note: needed as when saving a new version of a Document, a new record is saved in the repository_document
     * 		 table, and the 'hash' field must be unique.
     *
     * @return boolean
     */
    private function duplicate_current_file()
    {
        $full_current_file_path = $this->get_full_path();

        if(file_exists($full_current_file_path))
        {
            $filename_hash        = md5($this->get_filename());
            $relative_folder_path = $this->get_owner_id() . '/' . Text :: char_at($filename_hash, 0);
            $full_folder_path     = Path :: get(SYS_REPO_PATH) . $relative_folder_path;

            $unique_filename_hash = Filesystem :: create_unique_name($full_folder_path, $filename_hash);

            $path_to_copied_file  = $full_folder_path . '/' . $unique_filename_hash;

            $this->set_path($relative_folder_path . '/' . $unique_filename_hash);
            $this->set_hash($unique_filename_hash);

            return copy($full_current_file_path, $path_to_copied_file);
        }
        else
        {
            return false;
        }
    }

    /*************************************************************************/
	/*** Active record functions *********************************************/
	/*************************************************************************/

    /**
     * (non-PHPdoc)
     * @see repository/lib/ContentObject#create()
     */
    function create()
    {
        /*
         * To do when the object is created:
         * - check has title
         * - check has description
         * - check has owner
         * - check has tmp file path ? OR file content in memory ? OR file url to retrieve ?
         *
         * - set parent type to 'document'
         * - move file to the current owner id path
         * - save properties in content_object table
         * - get generated id from content_object table
         * - save properties in document table
         */

        $this->clear_errors();

        if($this->check_before_save()) //may be called twice in some situation (if the calling method is 'save() from the DataClass), but the create() method in the content_object class doesn't call it
        {
            return parent :: create();
        }
        else
        {
            return false;
        }
    }

    /**
     * (non-PHPdoc)
     * @see repository/lib/ContentObject#update($trueUpdate)
     */
    function update($trueUpdate = true)
    {
        /*
         * Force using version() instead of update() if the object is marked to be saved as a new version
         */
        if($this->save_as_new_version)
        {
            return $this->version();
        }

        $this->clear_errors();

        if($this->check_before_save()) //may be called twice in some situation (if the calling method is 'save() from the DataClass), but the create() method in the content_object class doesn't call it
        {
            return parent :: update($trueUpdate);
        }
        else
        {
            return false;
        }
    }

    function version($trueUpdate = true)
    {
        $this->clear_errors();

        if($this->check_before_save())
        {
            return parent :: version($trueUpdate);
        }
        else
        {
            return false;
        }
    }

}
?>