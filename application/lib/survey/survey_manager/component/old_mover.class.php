<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';


class SurveyManagerMoverComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
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
        
        $publication = $this->retrieve_survey_publication($pid);
        
        if (! $publication->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed($trail, false);
        }
        
        $parent = $publication->get_category();
        
        $form = $this->build_move_form($parent, $pids);
        if ($form->validate())
        {
            $new_category_id = $this->move_publications_to_category($form, $pids);
            $this->redirect(Translation :: get('CategoriesMoved'), false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS, 'category' => $new_category_id));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            //$trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
            //$trail->add(new Breadcrumb($this->get_move_survey_publication_url($publication), Translation :: get('MoveSurveyPublications')));
            
            $this->display_header($trail, true);
            
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function build_move_form($exclude_category, $pids)
    {
        $url = $this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pids));
        $form = new FormValidator('survey_publication_mover', 'post', $url);
        
        $this->categories = array();
        $this->categories[0] = Translation :: get('Root');
        
        $this->retrieve_categories_recursive(0, $exclude_category);
        
        $form->addElement('select', SurveyPublication :: PROPERTY_CATEGORY, Translation :: get('SelectCategory'), $this->categories);
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive finish'));
        
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        $conditions[] = new NotCondition(new EqualityCondition(SurveyPublicationCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(SurveyPublicationCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);
        
        $cats = SurveyDataManager :: get_instance()->retrieve_survey_publication_categories($condition);
        while ($cat = $cats->next_result())
        {
            $this->categories[$cat->get_id()] = str_repeat('--', $level) . ' ' . $cat->get_name();
            $this->retrieve_categories_recursive($cat->get_id(), $exclude_category, ($level + 1));
        }
    }

    function move_publications_to_category($form, $pids)
    {
        $category = $form->exportValue(SurveyPublication :: PROPERTY_CATEGORY);
        
        if (! is_array($pids))
        {
            $pids = array($pids);
        }
        
        $condition = new InCondition(SurveyPublication :: PROPERTY_ID, $pids);
        $publications = SurveyDataManager :: get_instance()->retrieve_survey_publications($condition);
        while ($publication = $publications->next_result())
        {
            $publication->set_category($category);
            $publication->update();
        }
        
        return $category;
    }
}
?>