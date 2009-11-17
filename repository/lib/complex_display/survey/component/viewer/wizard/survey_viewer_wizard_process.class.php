<?php
/**
 * $Id: survey_viewer_wizard_process.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard
 */
class SurveyViewerWizardProcess extends HTML_QuickForm_Action
{
    private $parent;

    public function SurveyViewerWizardProcess($parent)
    {
        $this->parent = $parent;
    }

    function perform($page, $actionName)
    {
        echo '<div class="assessment">';
        echo '<h2>' . $this->parent->get_survey()->get_title() . '</h2>';
        
        if ($this->parent->get_survey()->has_description())
        {
            echo '<div class="description">';
            echo $this->parent->get_survey()->get_description();
            echo '<div class="clear"></div>';
            echo '</div>';
        }
        echo '</div>';
        
        foreach ($_POST as $key => $value)
        {
            $value = Security :: remove_XSS($value);
            $split_key = split('_', $key);
            $question_id = $split_key[0];
            
            if (is_numeric($question_id))
            {
                $answer_index = $split_key[1];
                $values[$question_id][$answer_index] = $value;
            }
        }
        
        //$question_numbers = $_SESSION['questions'];
        

        $rdm = RepositoryDataManager :: get_instance();
        
        $questions_cloi = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->parent->get_survey()->get_id()));
        
        while ($question_cloi = $questions_cloi->next_result())
        {
            $answers = $values[$question_cloi->get_id()];
            $this->parent->get_parent()->save_answer($question_cloi->get_id(), serialize($answers));
        }
        
        echo '<div class="assessment">';
        echo '<div class="description">';
        echo $this->parent->get_survey()->get_finish_text();
        echo '</div></div>';
        
        $back_url = $this->parent->get_parent()->get_go_back_url();
        
        if (! Request :: get('invitation_id'))
            echo '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';
    
    }
}
?>