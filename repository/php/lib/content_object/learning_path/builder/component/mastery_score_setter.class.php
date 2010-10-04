<?php
/**
 * $Id: mastery_score_setter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */

class LearningPathBuilderMasteryScoreSetterComponent extends LearningPathBuilder
{

    function run()
    {
        $complex_content_object_item_id = Request :: get(LearningPathBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(LearningPathBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();
        
        $parameters = array(LearningPathBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item, LearningPathBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id);
        
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('BuildPrerequisites')));
        
        if (! $complex_content_object_item_id)
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->display_footer();
            exit();
        }
        
        $selected_complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);
        $lp_item = RepositoryDataManager :: get_instance()->retrieve_content_object($selected_complex_content_object_item->get_ref());
        $form = $this->get_form($this->get_url($parameters), $lp_item);
        
        if ($form->validate())
        {
            $succes = $this->set_mastery_score($lp_item, $form->exportValues());
            $message = $succes ? 'MasteryScoreSet' : 'MasteryScoreNotSet';
            $this->redirect(Translation :: get($message), ! $succes, array_merge($parameters, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE)));
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