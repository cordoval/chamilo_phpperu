<?php
/**
 * @package repository.lib
 */
/**
 *	@author Sven Vanpoucke
 */

class ContentObjectShare extends DataClass
{
    const SEARCH_RIGHT = 1;
    const VIEW_RIGHT = 2;
    const USE_RIGHT = 3;
    const REUSE_RIGHT = 4;
    
	const CLASS_NAME = __CLASS__;
    
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_RIGHT_ID = 'right_id';

    const PARAM_TYPE = 'share_type';

    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }
    
 	function get_right_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHT_ID);
    }

    function set_right_id($right_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHT_ID, $right_id);
    }

    /**
     * Get the default properties of all groups.
     * @return array The property names.
     */
    static function get_default_property_names($additional_property_names = array())
    {
        $additional_property_names[] = self :: PROPERTY_CONTENT_OBJECT_ID;
        $additional_property_names[] = self :: PROPERTY_RIGHT_ID;
    	return $additional_property_names;
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
    static function get_rights()
    {
    	$rights = array();
    	
    	if(!PlatformSetting :: get('all_objects_searchable', RepositoryManager :: APPLICATION_NAME))
    	{
			$rights[self :: SEARCH_RIGHT] = Translation :: get('Search');
    	}
		
		$rights[self :: VIEW_RIGHT] = Translation :: get('View');
		$rights[self :: USE_RIGHT] = Translation :: get('Use');
		$rights[self :: REUSE_RIGHT] = Translation :: get('Reuse');
		
		return $rights;
    }

    function has_right($right_id)
    {
        return $this->get_right_id() >= $right_id;
    }
//    function has_search_right()
//    {
//    	return $this->get_right() >= self :: SEARCH_RIGHT;
//    }
//
//    function has_view_right()
//    {
//    	return $this->get_right() >= self :: VIEW_RIGHT;
//    }
//
//    function has_use_right()
//    {
//    	return $this->get_right() >= self :: USE_RIGHT;
//    }
//
//	function has_reuse_right()
//    {
//    	return $this->get_right() >= self :: REUSE_RIGHT;
//    }
   
}
?>