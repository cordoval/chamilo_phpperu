<?php
/**
 * $Id: assessment_publication_category.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.category_manager
 */
require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../assessment_data_manager.class.php';

/**
 * @package category
 */
/**
 *	@author Sven Vanpoucke
 */

class AssessmentPublicationCategory extends PlatformCategory
{

    function create()
    {
        $adm = AssessmentDataManager :: get_instance();
        $this->set_display_order($adm->select_next_assessment_publication_category_display_order($this->get_parent()));
        $succes = $adm->create_assessment_publication_category($this);
        
        if(!$succes)
        {
        	return false;
        }
        
    	$parent = $this->get_parent();
        if ($parent == 0)
        {
            $parent_id = AssessmentRights :: get_assessments_subtree_root_id();
        }
        else
        {
            $parent_id = AssessmentRights :: get_location_id_by_identifier_from_assessments_subtree($this->get_parent(), AssessmentRights :: TYPE_CATEGORY); 
        }
        
    	return AssessmentRights :: create_location_in_assessments_subtree($this->get_name(), $this->get_id(), $parent_id, AssessmentRights :: TYPE_CATEGORY);
    }

    function update($move = false)
    {
        $succes = AssessmentDataManager :: get_instance()->update_assessment_publication_category($this);
        if(!$succes)
        {
        	return false;
        }
        
    	if($move)
        {
        	if($this->get_parent())
        	{
        		$new_parent_id = AssessmentRights :: get_location_id_by_identifier_from_assessments_subtree($this->get_parent(), AssessmentRights :: TYPE_CATEGORY);
        	}
        	else
        	{
        		$new_parent_id = AssessmentRights :: get_assessments_subtree_root_id();	
        	}
        	
        	$location = AssessmentRights :: get_location_by_identifier_from_assessments_subtree($this->get_id(), AssessmentRights :: TYPE_CATEGORY);
        	return $location->move($new_parent_id);
        }
        
        return true;
        
    }

    function delete()
    {
        $location = AssessmentRights :: get_location_by_identifier_from_assessments_subtree($this->get_id(), AssessmentRights :: TYPE_CATEGORY);
		if($location)
		{
			if(!$location->remove())
			{
				return false;
			}
		}
		
    	return AssessmentDataManager :: get_instance()->delete_assessment_publication_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(__CLASS__);
    }
}