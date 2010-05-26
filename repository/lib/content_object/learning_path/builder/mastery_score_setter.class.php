<?php
/**
 * $Id: mastery_score_setter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';

class LearningPathBuilderMasteryScoreSetterComponent extends LearningPathBuilderComponent
{

    function run()
    {
        $cloi_id = Request :: get(LearningPathBuilder :: PARAM_SELECTED_CLOI_ID);
        $parent_cloi = Request :: get(LearningPathBuilder :: PARAM_CLOI_ID);
        
        $menu_trail = $this->get_clo_breadcrumbs();
        $trail = new BreadcrumbTrail(false);
        $trail->merge($menu_trail);
        
        $parameters = array(LearningPathBuilder :: PARAM_ROOT_CONTENT_OBJECT => $this->get_root_content_object()->get_id(), LearningPathBuilder :: PARAM_CLOI_ID => $parent_cloi, LearningPathBuilder :: PARAM_SELECTED_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish'));
        
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('BuildPrerequisites')));
        
        if (! $cloi_id)
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->display_footer();
            exit();
        }
        
        $selected_cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($cloi_id);
        $lp_item = RepositoryDataManager :: get_instance()->retrieve_content_object($selected_cloi->get_ref());
        $form = $this->get_form($this->get_url($parameters), $lp_item);
        
        if ($form->validate())
        {
            $succes = $this->set_mastery_score($lp_item, $form->exportValues());
            $message = $succes ? 'MasteryScoreSet' : 'MasteryScoreNotSet';
            $this->redirect(Translation :: get($message), ! $succes, array_merge($parameters, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO)));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    
    }

    function get_form($url, $lp_item)
    {
        $form = new Formvalidator('mastery_score', 'post', $url);
        
        $values = array();
        for($i = 0; $i <= 100; $i ++)
            $values[$i] = $i;
        
        $form->addElement('select', 'mastery_score', Translation :: get('MasteryScore'), $values);
        
        if ($lp_item->get_mastery_score())
        {
            $form->setDefaults(array('mastery_score' => $lp_item->get_mastery_score()));
        }
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('SetMasteryScore'), array('class' => 'positive'));
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        return $form;
    }

    function set_mastery_score($lp_item, $values)
    {
        $lp_item->set_mastery_score($values['mastery_score']);
        return $lp_item->update();
    }
}

?>