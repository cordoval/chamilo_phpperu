<?php
/**
 * $Id: dokeos185_dropbox_file.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_dropbox_file.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/document/document.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once 'dokeos185_item_property.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/category_manager/content_object_publication_category.class.php';

/**
 * This class presents a Dokeos185 dropbox_file
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxFile extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    private $item_property;
    
    private static $files = array();
    
    /**
     * Dokeos185DropboxFile properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_UPLOADER_ID = 'uploader_id';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_FILESIZE = 'filesize';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_AUTHOR = 'author';
    const PROPERTY_UPLOAD_DATE = 'upload_date';
    const PROPERTY_LAST_UPLOAD_DATE = 'last_upload_date';
    const PROPERTY_CAT_ID = 'cat_id';
    const PROPERTY_SESSION_ID = 'session_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185DropboxFile object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185DropboxFile($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_UPLOADER_ID, self :: PROPERTY_FILENAME, self :: PROPERTY_FILESIZE, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_AUTHOR, self :: PROPERTY_UPLOAD_DATE, self :: PROPERTY_LAST_UPLOAD_DATE, self :: PROPERTY_CAT_ID, self :: PROPERTY_SESSION_ID);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this Dokeos185DropboxFile.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the uploader_id of this Dokeos185DropboxFile.
     * @return the uploader_id.
     */
    function get_uploader_id()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOADER_ID);
    }

    /**
     * Returns the filename of this Dokeos185DropboxFile.
     * @return the filename.
     */
    function get_filename()
    {
        return $this->get_default_property(self :: PROPERTY_FILENAME);
    }

    /**
     * Returns the filesize of this Dokeos185DropboxFile.
     * @return the filesize.
     */
    function get_filesize()
    {
        return $this->get_default_property(self :: PROPERTY_FILESIZE);
    }

    /**
     * Returns the title of this Dokeos185DropboxFile.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this Dokeos185DropboxFile.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the author of this Dokeos185DropboxFile.
     * @return the author.
     */
    function get_author()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR);
    }

    /**
     * Returns the upload_date of this Dokeos185DropboxFile.
     * @return the upload_date.
     */
    function get_upload_date()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOAD_DATE);
    }

    /**
     * Returns the last_upload_date of this Dokeos185DropboxFile.
     * @return the last_upload_date.
     */
    function get_last_upload_date()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_UPLOAD_DATE);
    }

    /**
     * Returns the cat_id of this Dokeos185DropboxFile.
     * @return the cat_id.
     */
    function get_cat_id()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ID);
    }

    /**
     * Returns the session_id of this Dokeos185DropboxFile.
     * @return the session_id.
     */
    function get_session_id()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_ID);
    }

    /**
     * Check if the dropboxfile is valid
     * @param array $courses the parameters for the validation
     */
    
    function is_valid($courses)
    {
        $course = $courses['course'];
        $mgdm = MigrationDataManager :: get_instance();
        $old_mgdm = $courses['old_mgdm'];
        $this->item_property = $old_mgdm->get_item_property($course->get_db_name(), 'dropbox', $this->get_id());
        
        $filename = $this->get_filename();
        $old_rel_path = 'courses/' . $course->get_directory() . '/dropbox/';
        
        $filename = iconv("UTF-8", "ISO-8859-1", $filename);
        $old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);
        
        if (! $this->get_id())
        {
            //echo 'Error in ID at course : ' . $course->get_db_name() .' ';
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.dropbox_file');
            return false;
        }
        else 
            if (! $this->item_property)
            {
                //echo 'Error in property at course : ' . $course->get_db_name() .' ';
                $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.dropbox_file');
                return false;
            }
            else 
                if (! $this->item_property->get_ref())
                {
                    //echo 'Error in reference at course : ' . $course->get_db_name() .' ';
                    $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.dropbox_file');
                    return false;
                }
                else 
                    if (! $this->item_property->get_insert_date())
                    {
                        //echo 'Error in insert_date at course : ' . $course->get_db_name() .' ';
                        $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.dropbox_file');
                        return false;
                    }
                    else 
                        if (! file_exists($old_mgdm->append_full_path(false, $old_rel_path . $filename)))
                        {
                            //echo 'Error in full_path at course : ' . $course->get_db_name() .'ID : ' . $this->get_id();
                            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.dropbox_file');
                            return false;
                        }
        
        //Close the logfile
        /*$this->passedtime = $this->logfile->write_passed_time();
		$this->logfile->close_file();*/
        
        return true;
    }

    /**
     * Convert to new dropbox file
     * @param array $array the parameters for the conversion
     * @return the new dropbox file
     */
    function convert_data
    {
        $mgdm = MigrationDataManager :: get_instance();
        $old_mgdm = $array['old_mgdm'];
        if ($this->get_uploader_id())
            $new_user_id = $mgdm->get_id_reference($this->get_uploader_id(), 'user_user');
        else
            $new_user_id = $mgdm->get_id_reference($this->item_property->get_insert_user_id(), 'user_user');
        
        $course = $array['course'];
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        
        }
        
        $new_path = $new_user_id . '/';
        $old_rel_path = 'courses/' . $course->get_directory() . '/dropbox/';
        
        $new_rel_path = 'files/repository/' . $new_path;
        
        $lcms_document = null;
        
        $filename = iconv("UTF-8", "ISO-8859-1", $this->get_filename());
        $old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);
        
        $document_md5 = md5_file($old_mgdm->append_full_path(false, $old_rel_path . $filename));
        $document_id = $mgdm->get_document_from_md5($new_user_id, $document_md5);
        
        if (! $document_id)
        {
            $file = $old_mgdm->move_file($old_rel_path, $new_rel_path, $filename);
            
            if ($file)
            {
                //document parameters
                $lcms_document = new Document();
                
                $lcms_document->set_filesize($this->get_filesize());
                if ($this->get_title())
                    $lcms_document->set_title($this->get_title());
                else
                    $lcms_document->set_title($filename);
                $lcms_document->set_description('...');
                
                $lcms_document->set_owner_id($new_user_id);
                $lcms_document->set_creation_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
                $lcms_document->set_modification_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
                $lcms_document->set_path($new_path . $file);
                $lcms_document->set_filename($file);
                
                // Category for announcements already exists?
                $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('dropboxes'));
                if (! $lcms_category_id)
                {
                    //Create category for tool in lcms
                    $lcms_repository_category = new Category();
                    $lcms_repository_category->set_owner_id($new_user_id);
                    $lcms_repository_category->set_title(Translation :: get('dropboxes'));
                    $lcms_repository_category->set_description('...');
                    
                    //Retrieve repository id from dropbox
                    $repository_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('MyRepository'));
                    $lcms_repository_category->set_parent_id($repository_id);
                    
                    //Create category in database
                    $lcms_repository_category->create();
                    
                    $lcms_document->set_parent_id($lcms_repository_category->get_id());
                }
                else
                {
                    $lcms_document->set_parent_id($lcms_category_id);
                }
                
                if ($this->item_property->get_visibility() == 2)
                    $lcms_document->set_state(1);
                    
                //create document in database
                $lcms_document->create_all();
                
                $mgdm->add_file_md5($new_user_id, $lcms_document->get_id(), $document_md5);
            }
            else
            {
                $document_id = $mgdm->get_document_id($new_rel_path . $filename, $new_user_id);
                if ($document_id)
                {
                    $lcms_document = new ContentObject();
                    $lcms_document->set_id($document_id);
                }
            }
        
        }
        else
        {
            $lcms_document = new ContentObject();
            $lcms_document->set_id($document_id);
        }
        /*	
		//publication
		if($this->item_property->get_visibility() <= 1 && $lcms_document) 
		{
			// Categories already exists?
			$file_split = array();
			$file_split = split('/', $old_path);
			
			array_shift($file_split);
			array_pop($file_split);
			
			$parent = 0;
			
			foreach($file_split as $cat)
			{
				$lcms_category_id = self :: $mgdm->publication_category_exist($cat, $new_course_code,
					'document',$parent);
				
				if(!$lcms_category_id)
				{
					//Create category for tool in lcms
					$lcms_category = new ContentObjectPublicationCategory();
					$lcms_category->set_title($cat);
					$lcms_category->set_course($new_course_code);
					$lcms_category->set_tool('document');
					$lcms_category->set_parent_category_id($parent);
					
					//Create category in database
					$lcms_category->create();
					$parent = $lcms_category->get_id();
				}
				else
				{
					$parent = $lcms_category_id;
				}
				
			}	
		
			$publication = new ContentObjectPublication();
			
			$publication->set_content_object($lcms_document);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('document');
			$publication->set_category_id($parent);
			//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			//$publication->set_modified_date(0);
			//$publication->set_display_order_index($this->get_display_order());
			$publication->set_display_order_index(0);
			$publication->set_email_sent(0);
			
			$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
			
			//create publication in database
			$publication->create();		
		}
		*/
        
        return $lcms_document;
    }

    /**
     * Retrieve all dropbox files from the database
     * @param array $parameters parameters for the retrieval
     * @return array of dropbox files
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'dropbox';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'dropbox_file';
        $classname = 'Dokeos185DropboxFile';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'dropbox_file';
        return $array;
    }
}

?>