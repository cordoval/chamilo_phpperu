<?php
/**
 * $Id: dokeos185_assignment_submission.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_assignment_submission.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/document/document.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication_category.class.php';

/**
 * This class presents a Dokeos185 assignment_submission
 *
 * @author Sven Vanpoucke
 */
class Dokeos185AssignmentSubmission extends ImportAssignmentSubmission
{
    /**
     * Dokeos185AssignmentSubmission properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_ASSIGNMENT_ID = 'assignment_id';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_GROUP_ID = 'group_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_VISIBILITY = 'visibility';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_LAST_EDIT_DATE = 'last_edit_date';
    const PROPERTY_AUTHORS = 'authors';
    const PROPERTY_SUBMITTED_TEXT = 'submitted_text';
    const PROPERTY_SUBMITTED_DOC_PATH = 'submitted_doc_path';
    const PROPERTY_PRIVATE_FEEDBACK = 'private_feedback';
    const PROPERTY_ORIGINAL_AUTH_ID = 'original_auth_id';
    const PROPERTY_SCORE = 'score';
    
    private static $mgdm;
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185AssignmentSubmission object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185AssignmentSubmission($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_ASSIGNMENT_ID, self :: PROPERTY_PARENT_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_GROUP_ID, self :: PROPERTY_TITLE, self :: PROPERTY_VISIBILITY, self :: PROPERTY_CREATION_DATE, self :: PROPERTY_LAST_EDIT_DATE, self :: PROPERTY_AUTHORS, self :: PROPERTY_SUBMITTED_TEXT, self :: PROPERTY_SUBMITTED_DOC_PATH, self :: PROPERTY_PRIVATE_FEEDBACK, self :: PROPERTY_ORIGINAL_AUTH_ID, self :: PROPERTY_SCORE);
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
     * Returns the id of this Dokeos185AssignmentSubmission.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the assignment_id of this Dokeos185AssignmentSubmission.
     * @return the assignment_id.
     */
    function get_assignment_id()
    {
        return $this->get_default_property(self :: PROPERTY_ASSIGNMENT_ID);
    }

    /**
     * Returns the parent_id of this Dokeos185AssignmentSubmission.
     * @return the parent_id.
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Returns the user_id of this Dokeos185AssignmentSubmission.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the group_id of this Dokeos185AssignmentSubmission.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Returns the title of this Dokeos185AssignmentSubmission.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the visibility of this Dokeos185AssignmentSubmission.
     * @return the visibility.
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Returns the creation_date of this Dokeos185AssignmentSubmission.
     * @return the creation_date.
     */
    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    /**
     * Returns the last_edit_date of this Dokeos185AssignmentSubmission.
     * @return the last_edit_date.
     */
    function get_last_edit_date()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_EDIT_DATE);
    }

    /**
     * Returns the authors of this Dokeos185AssignmentSubmission.
     * @return the authors.
     */
    function get_authors()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHORS);
    }

    /**
     * Returns the submitted_text of this Dokeos185AssignmentSubmission.
     * @return the submitted_text.
     */
    function get_submitted_text()
    {
        return $this->get_default_property(self :: PROPERTY_SUBMITTED_TEXT);
    }

    /**
     * Returns the submitted_doc_path of this Dokeos185AssignmentSubmission.
     * @return the submitted_doc_path.
     */
    function get_submitted_doc_path()
    {
        return $this->get_default_property(self :: PROPERTY_SUBMITTED_DOC_PATH);
    }

    /**
     * Returns the private_feedback of this Dokeos185AssignmentSubmission.
     * @return the private_feedback.
     */
    function get_private_feedback()
    {
        return $this->get_default_property(self :: PROPERTY_PRIVATE_FEEDBACK);
    }

    /**
     * Returns the original_auth_id of this Dokeos185AssignmentSubmission.
     * @return the original_auth_id.
     */
    function get_original_auth_id()
    {
        return $this->get_default_property(self :: PROPERTY_ORIGINAL_AUTH_ID);
    }

    /**
     * Returns the score of this Dokeos185AssignmentSubmission.
     * @return the score.
     */
    function get_score()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE);
    }

    /**
     * Check if the assignment submission is valid
     * @param array $array the parameters for the validation
     * @return true if the assignment submission is valid 
     */
    function is_valid($array)
    {
        $old_mgdm = $array['old_mgdm'];
        
        $course = $array['course'];
        $filename = $this->get_submitted_doc_path();
        $old_rel_path = 'courses/' . $course->get_directory() . '/assignment/assig_' . $this->get_assignment_id() . '/';
        
        $filename = iconv("UTF-8", "ISO-8859-1", $filename);
        $old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);
        
        if (! $this->get_assignment_id() || ! $this->get_submitted_doc_path() || ! file_exists($old_mgdm->append_full_path(false, $old_rel_path . $filename)))
        {
            $mgdm = MigrationDataManager :: get_instance();
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.assignment_submission');
            return false;
        }
        return true;
    }

    /**
     * Convert to new assignment submission
     * Create document
     * @param array $array the parameters for the conversion
     * @return the new assignment submission
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
        $old_mgdm = $array['old_mgdm'];
        $mgdm = MigrationDataManager :: get_instance();
        
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        $new_user_id = $mgdm->get_id_reference($this->get_user_id(), 'user_user');
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        $filename = $this->get_submitted_doc_path();
        $new_path = $new_user_id . '/';
        $old_rel_path = 'courses/' . $course->get_directory() . '/assignment/assig_' . $this->get_assignment_id() . '/';
        
        $new_rel_path = 'files/repository/' . $new_path;
        
        $lcms_document = null;
        
        $filename = iconv("UTF-8", "ISO-8859-1", $filename);
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
                if (! $this->get_title())
                    $lcms_document->set_title($filename);
                else
                    $lcms_document->set_title($this->get_title());
                if (! $this->get_submitted_text())
                    $lcms_document->set_description('...');
                else
                    $lcms_document->set_description($this->get_submitted_text());
                
                $lcms_document->set_owner_id($new_user_id);
                
                $lcms_document->set_path($new_path . $file);
                $lcms_document->set_filename($file);
                
                $lcms_document->set_creation_date($mgdm->make_unix_time($this->get_creation_date()));
                $lcms_document->set_modification_date($mgdm->make_unix_time($this->get_last_edit_date()));
                
                //if($this->get_visibility() == 2)
                //	$lcms_document->set_state(1);
                

                // Category for announcements already exists?
                $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('assignment'));
                if (! $lcms_category_id)
                {
                    //Create category for tool in lcms
                    $lcms_repository_category = new Category();
                    $lcms_repository_category->set_owner_id($new_user_id);
                    $lcms_repository_category->set_title(Translation :: get('assignment'));
                    $lcms_repository_category->set_description('...');
                    
                    //Retrieve repository id from course
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
                
                //create document in database
                $lcms_document->create();
                
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
        }
        
        //publication
        /*
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
					'assignment',$parent);
				
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
			
			$end_time_cat = Logger :: get_microtime();
			$passedtime_categories = $end_time_cat - $start_time_cat;
		
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
     * Retrieve all assignment submission from the database
     * @param array $parameters parameters for the retrieval
     * @return array of assignment submissions
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'assignment_submission';
        $classname = 'Dokeos185AssignmentSubmission';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'assignment_submission';
        return $array;
    }
}

?>
