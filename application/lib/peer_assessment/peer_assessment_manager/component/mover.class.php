<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../category_manager/peer_assessment_publication_category.class.php';

/**
 * Component to create a new peer_assessment_publication object
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerMoverComponent extends PeerAssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get('peer_assessment_publication');
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
        
        $publication = $this->retrieve_peer_assessment_publication($pid);      
        if (! $publication->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed($trail, false);
        }
        
        $parent = $publication->get_category();
        
        $form = $this->build_move_form($parent, $pids);
        if ($form->validate())
        {
            $new_category_id = $this->move_publications_to_category($form, $pids);
            $this->redirect(Translation :: get('CategoriesMoved'), false, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS, 'category' => $new_category_id));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_MOVE_PEER_ASSESSMENT_PUBLICATION, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('MovePeerAssessmentPublications')));
            
            $this->display_header($trail, true);
            
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function build_move_form($exclude_category, $pids)
    {
        $url = $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pids));
        $form = new FormValidator('peer_assessment_publication_mover', 'post', $url);
        
        $this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        
        $this->retrieve_categories_recursive(0, $exclude_category);
        
        $form->addElement('select', PeerAssessmentPublication :: PROPERTY_CATEGORY, Translation :: get('SelectCategory'), $this->categories);
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive finish'));
        
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        $conditions[] = new NotCondition(new EqualityCondition(PeerAssessmentPublicationCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(PeerAssessmentPublicationCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cats = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_categories($condition);
        while ($cat = $cats->next_result())
        {
            $this->categories[$cat->get_id()] = str_repeat('--', $level) . ' ' . $cat->get_name();
            $this->retrieve_categories_recursive($cat->get_id(), $exclude_category, ($level + 1));
        }
    }

    function move_publications_to_category($form, $pids)
    {
        $category = $form->exportValue(PeerAssessmentPublication :: PROPERTY_CATEGORY);
        
        if (! is_array($pids))
            $pids = array($pids);
        
        $condition = new InCondition(PeerAssessmentPublication :: PROPERTY_ID, $pids);
        $publications = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publications($condition);
        while ($publication = $publications->next_result())
        {
            $publication->set_category($category);
            $publication->update();
        }
        
        return $category;
    }
}
?>