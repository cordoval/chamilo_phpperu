<?php
/**
 * $Id: dokeos185_lp_item.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 lp_item
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpItem extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'lp_item';
    
    /**
     * Dokeos185LpItem properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_LP_ID = 'lp_id';
    const PROPERTY_ITEM_TYPE = 'item_type';
    const PROPERTY_REF = 'ref';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_PATH = 'path';
    const PROPERTY_MIN_SCORE = 'min_score';
    const PROPERTY_MAX_SCORE = 'max_score';
    const PROPERTY_MASTERY_SCORE = 'mastery_score';
    const PROPERTY_PARENT_ITEM_ID = 'parent_item_id';
    const PROPERTY_PREVIOUS_ITEM_ID = 'previous_item_id';
    const PROPERTY_NEXT_ITEM_ID = 'next_item_id';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_PREREQUISITE = 'prerequisite';
    const PROPERTY_PARAMETERS = 'parameters';
    const PROPERTY_LAUNCH_DATA = 'launch_data';
    const PROPERTY_MAX_TIME_ALLOWED = 'max_time_allowed';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_LP_ID, self :: PROPERTY_ITEM_TYPE, self :: PROPERTY_REF, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PATH, self :: PROPERTY_MIN_SCORE, self :: PROPERTY_MAX_SCORE, self :: PROPERTY_MASTERY_SCORE, self :: PROPERTY_PARENT_ITEM_ID, self :: PROPERTY_PREVIOUS_ITEM_ID, self :: PROPERTY_NEXT_ITEM_ID, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_PREREQUISITE, self :: PROPERTY_PARAMETERS, self :: PROPERTY_LAUNCH_DATA, self :: PROPERTY_MAX_TIME_ALLOWED);
    }
    
    /**
     * Returns the id of this Dokeos185LpItem.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the lp_id of this Dokeos185LpItem.
     * @return the lp_id.
     */
    function get_lp_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_ID);
    }

    /**
     * Returns the item_type of this Dokeos185LpItem.
     * @return the item_type.
     */
    function get_item_type()
    {
        return $this->get_default_property(self :: PROPERTY_ITEM_TYPE);
    }

    /**
     * Returns the ref of this Dokeos185LpItem.
     * @return the ref.
     */
    function get_ref()
    {
        return $this->get_default_property(self :: PROPERTY_REF);
    }

    /**
     * Returns the title of this Dokeos185LpItem.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this Dokeos185LpItem.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the path of this Dokeos185LpItem.
     * @return the path.
     */
    function get_path()
    {
        return $this->get_default_property(self :: PROPERTY_PATH);
    }

    /**
     * Returns the min_score of this Dokeos185LpItem.
     * @return the min_score.
     */
    function get_min_score()
    {
        return $this->get_default_property(self :: PROPERTY_MIN_SCORE);
    }

    /**
     * Returns the max_score of this Dokeos185LpItem.
     * @return the max_score.
     */
    function get_max_score()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_SCORE);
    }

    /**
     * Returns the mastery_score of this Dokeos185LpItem.
     * @return the mastery_score.
     */
    function get_mastery_score()
    {
        return $this->get_default_property(self :: PROPERTY_MASTERY_SCORE);
    }

    /**
     * Returns the parent_item_id of this Dokeos185LpItem.
     * @return the parent_item_id.
     */
    function get_parent_item_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ITEM_ID);
    }

    /**
     * Returns the previous_item_id of this Dokeos185LpItem.
     * @return the previous_item_id.
     */
    function get_previous_item_id()
    {
        return $this->get_default_property(self :: PROPERTY_PREVIOUS_ITEM_ID);
    }

    /**
     * Returns the next_item_id of this Dokeos185LpItem.
     * @return the next_item_id.
     */
    function get_next_item_id()
    {
        return $this->get_default_property(self :: PROPERTY_NEXT_ITEM_ID);
    }

    /**
     * Returns the display_order of this Dokeos185LpItem.
     * @return the display_order.
     */
    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the prerequisite of this Dokeos185LpItem.
     * @return the prerequisite.
     */
    function get_prerequisite()
    {
        return $this->get_default_property(self :: PROPERTY_PREREQUISITE);
    }

    /**
     * Returns the parameters of this Dokeos185LpItem.
     * @return the parameters.
     */
    function get_parameters()
    {
        return $this->get_default_property(self :: PROPERTY_PARAMETERS);
    }

    /**
     * Returns the launch_data of this Dokeos185LpItem.
     * @return the launch_data.
     */
    function get_launch_data()
    {
        return $this->get_default_property(self :: PROPERTY_LAUNCH_DATA);
    }

    /**
     * Returns the max_time_allowed of this Dokeos185LpItem.
     * @return the max_time_allowed.
     */
    function get_max_time_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_TIME_ALLOWED);
    }

    /**
     * Check if the lp item is valid
     * @return true if the lp item is valid 
     */
    function is_valid()
    {
    	if($this->get_item_type() == 'forum')
        {
        	$this->set_default_property(self :: PROPERTY_ITEM_TYPE, 'forum_forum');
        }
        
    	$new_learning_path_id = $this->get_id_reference($this->get_lp_id(), $this->get_database_name() . '.lp');
        $reference_content_object_id = $this->get_id_reference($this->get_path(), $this->get_database_name() . '.' . $this->get_item_type());
        $parent_item_id = $this->get_id_reference($this->get_parent_item_id(), $this->get_database_name() . '.lp_item');
        
        if (! $this->get_id() || ! $this->get_item_type() || ! ($this->get_title() || $this->get_description()) || !$new_learning_path_id || 
        	  ($this->get_item_type() != 'dokeos_chapter' && !$reference_content_object_id) || ($this->get_parent_item_id() > 0 && !$parent_item_id))
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'learning_path_item', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new lp item
     */
    function convert_data()
    {
        $course = $this->get_course();
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
        
        $new_learning_path_id = $this->get_id_reference($this->get_lp_id(), $this->get_database_name() . '.lp');
        $learning_path = RepositoryDataManager :: get_instance()->retrieve_content_object($new_learning_path_id, 'learning_path');
        
		$new_user_id = $learning_path->get_owner_id();
        
        $chamilo_learning_path_item = new LearningPathItem();

        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('LearningPaths'));
        $chamilo_learning_path_item->set_parent_id($chamilo_category_id);
        
        $reference_content_object_id = $this->get_id_reference($this->get_path(), $this->get_database_name() . '.' . $this->get_item_type());
        
       	if($this->get_item_type() == 'dokeos_chapter')
       	{
       		$new_object = $this->create_new_learning_path($learning_path);
       	}
       	else 
       	{
	        $new_object = $this->create_new_learning_path_item($learning_path);
       	}
       	
    	if(!$this->get_parent_item_id())
        {
       		$parent_lp = $learning_path->get_id();
        }
       	else
       	{ 
       		$parent_lp = $this->get_id_reference($this->get_parent_item_id(), $this->get_database_name() . '.lp_item');
       	}
       	
        if($parent_lp)
        {
        	$prerequisites = $this->get_id_reference($this->get_prerequisite(), $this->get_database_name() . '.lp_item');
        	$this->create_complex_content_object_item($new_object, $parent_lp, $new_user_id, null, $this->get_display_order(), array('prerequisites' => $prerequisites));
        }
        
        $this->create_id_reference($this->get_id(), $new_object->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'learning_path_item', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $new_object->get_id())));
    }
    
    /**
     * Creates a new learning path from a dokeos chapter
     * @param LearningPath $main_learning_path
     */
    private function create_new_learning_path($main_learning_path)
    {
		$chamilo_learning_path = new LearningPath();
       		
       	$chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($main_learning_path->get_owner_id(), Translation :: get('LearningPaths'));
        $chamilo_learning_path->set_parent_id($chamilo_category_id);
        
        if (! $this->get_title())
        {
            $chamilo_learning_path->set_title(substr($this->get_description(), 0, 20));
        }
        else
        {
            $chamilo_learning_path->set_title($this->get_title());
        }
        
        if (! $this->get_description())
        {
            $chamilo_learning_path->set_description($this->get_title());
        }
        else
        {
            $chamilo_learning_path->set_description($this->get_description());
        }
        
        $chamilo_learning_path->set_owner_id($main_learning_path->get_owner_id());
        $chamilo_learning_path->set_version('chamilo');
        $chamilo_learning_path->set_creation_date($main_learning_path->get_creation_date());
        $chamilo_learning_path->set_modification_date($main_learning_path->get_modification_date());
        
        //create item in database
        $chamilo_learning_path->create_all();

        return $chamilo_learning_path;
    }
    
    private function create_new_learning_path_item($main_learning_path)
    {
    	$chamilo_learning_path_item = new LearningPathItem();
    	
       	$chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($main_learning_path->get_owner_id(), Translation :: get('LearningPaths'));
        $chamilo_learning_path_item->set_parent_id($chamilo_category_id);
        
        if (! $this->get_title())
        {
            $chamilo_learning_path_item->set_title(substr($this->get_description(), 0, 20));
        }
        else
            $chamilo_learning_path_item->set_title($this->get_title());
        
        if (! $this->get_description())
            $chamilo_learning_path_item->set_description($this->get_title());
        else
            $chamilo_learning_path_item->set_description($this->get_description());
        
        $reference_content_object_id = $this->get_id_reference($this->get_path(), $this->get_database_name() . '.' . $this->get_item_type()); 
        $chamilo_learning_path_item->set_reference($reference_content_object_id);
        
        $chamilo_learning_path_item->set_owner_id($main_learning_path->get_owner_id());
        $chamilo_learning_path_item->set_creation_date($main_learning_path->get_creation_date());
        $chamilo_learning_path_item->set_modification_date($main_learning_path->get_modification_date());
        $chamilo_learning_path_item->set_mastery_score($this->get_mastery_score());
        
        //create item in database
        $chamilo_learning_path_item->create_all();
        
    	return $chamilo_learning_path_item;
    }

	static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
}

?>