<?php

namespace application\assessment;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\FormValidator;
use common\libraries\NotCondition;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\InCondition;
/**
 * $Id: mover.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerMoverComponent extends AssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get(self :: PARAM_ASSESSMENT_PUBLICATION);
        if (! $pid || (is_array($pid) && count($pid) == 0))
        {
            $this->not_allowed();
            exit();
        }
        $pids = $pid;
        
        if (is_array($pids))
        {
            $pid = $pids[0];
        }
        
        $publication = $this->retrieve_assessment_publication($pid);
        
        if (! $publication->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed(null, false);
        }
        
        $parent = $publication->get_category();
        
        $form = $this->build_move_form($parent, $pids);
        if ($form->validate())
        {
            $new_category_id = $this->move_publications_to_category($form, $pids);
            $this->redirect(Translation :: get('CategoriesMoved'), false, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS, 'category' => $new_category_id));
        }
        else
        {
            $this->display_header(null, true);
            echo $form->toHtml();
            $this->display_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('assessment_mover');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('AssessmentManagerBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_ASSESSMENT_PUBLICATION);
    }

    function build_move_form($exclude_category, $pids)
    {
        $url = $this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pids));
        $form = new FormValidator('assessment_publication_mover', 'post', $url);
        
        $this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        
        $this->retrieve_categories_recursive(0, $exclude_category);
        
        $form->addElement('select', AssessmentPublication :: PROPERTY_CATEGORY, Translation :: get('SelectCategory'), $this->categories);
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive finish'));
        
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        $conditions[] = new NotCondition(new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cats = AssessmentDataManager :: get_instance()->retrieve_assessment_publication_categories($condition);
        while ($cat = $cats->next_result())
        {
            $this->categories[$cat->get_id()] = str_repeat('--', $level) . ' ' . $cat->get_name();
            $this->retrieve_categories_recursive($cat->get_id(), $exclude_category, ($level + 1));
        }
    }

    function move_publications_to_category($form, $pids)
    {
        $category = $form->exportValue(AssessmentPublication :: PROPERTY_CATEGORY);
        
        if (! is_array($pids))
            $pids = array($pids);
        
        $condition = new InCondition(AssessmentPublication :: PROPERTY_ID, $pids);
        $publications = AssessmentDataManager :: get_instance()->retrieve_assessment_publications($condition);
        while ($publication = $publications->next_result())
        {
            if($publication->get_category() == $category)
            {
            	continue;
            }
            
        	$publication->set_category($category);
            $publication->update();
            
            if($category)
        	{
        		$new_parent_id = AssessmentRights :: get_location_id_by_identifier_from_assessments_subtree($category, AssessmentRights :: TYPE_CATEGORY);
        	}
        	else
        	{
        		$new_parent_id = AssessmentRights :: get_assessments_subtree_root_id();	
        	}
        	
        	$location = AssessmentRights :: get_location_by_identifier_from_assessments_subtree($publication->get_id(), AssessmentRights :: TYPE_PUBLICATION);
        	$location->move($new_parent_id);
            
        }
        
        return $category;
    }
}
?>