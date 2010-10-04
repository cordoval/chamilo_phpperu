<?php

/**
 * $Id: user_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package user
 */

require_once dirname(__FILE__) . '/assessment_manager/assessment_manager.class.php';

class AssessmentRights
{
    const PUBLISH_RIGHT = '1';
    const VIEW_RESULTS_RIGHT = '2';
    
    const TREE_TYPE_ASSESSMENT = 1;
    const TYPE_CATEGORY = 1;
    const TYPE_PUBLICATION = 2;
    
    static function get_available_rights_for_publications()
    {
    	return array('ViewResults' => self :: VIEW_RESULTS_RIGHT);
    }
    
    static function get_available_rights_for_categories()
    {
    	return array('Publish' => self :: PUBLISH_RIGHT);
    }
    
	static function create_location_in_assessments_subtree($name, $identifier, $parent, $type)
    {
    	return RightsUtilities :: create_location($name, AssessmentManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_ASSESSMENT);
    }
    
    static function get_assessments_subtree_root()
    {
    	return RightsUtilities :: get_root(AssessmentManager :: APPLICATION_NAME, self :: TREE_TYPE_ASSESSMENT, 0);
    }
    
	static function get_assessments_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(AssessmentManager :: APPLICATION_NAME, self :: TREE_TYPE_ASSESSMENT, 0);
    }
    
    static function get_location_id_by_identifier_from_assessments_subtree($identifier, $type)
    {
    	return RightsUtilities :: get_location_id_by_identifier(AssessmentManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_ASSESSMENT);
    }
    
	static function get_location_by_identifier_from_assessments_subtree($identifier, $type)
    {
    	return RightsUtilities :: get_location_by_identifier(AssessmentManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_ASSESSMENT);
    }
    
	static function is_allowed_in_assessments_subtree($right, $location, $type)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, AssessmentManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_ASSESSMENT);
    }
    
    static function create_assessments_subtree_root_location()
    {
    	return RightsUtilities :: create_location('assessments_tree', AssessmentManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_ASSESSMENT);
    }
}
?>