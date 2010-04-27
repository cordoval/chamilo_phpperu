<?php
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
        $pid = Request :: get('assessment_publication');
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
            $this->not_allowed($trail, false);
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
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
            $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_MOVE_ASSESSMENT_PUBLICATION, AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('MoveAssessmentPublications')));
            
            $this->display_header($trail, true);
            
            echo $form->toHtml();
            $this->display_footer();
        }
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
            $publication->set_category($category);
            $publication->update();
        }
        
        return $category;
    }
}
?>