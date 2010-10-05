<?php
/**
 * $Id: laika_wizard_process.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.inc.wizard
 */

require_once Path :: get_application_path() . 'lib/laika/laika_attempt.class.php';
require_once Path :: get_application_path() . 'lib/laika/laika_answer.class.php';
require_once Path :: get_application_path() . 'lib/laika/laika_result.class.php';
require_once Path :: get_application_path() . 'lib/laika/laika_calculated_result.class.php';

/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class LaikaWizardProcess extends HTML_QuickForm_Action
{
    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs.
     */
    public function LaikaWizardProcess($parent)
    {
        $this->parent = $parent;
    }

    function perform(& $page, $actionName)
    {
        $values = $page->controller->exportValues();
        $question_answers = $values['question'];
        
        $user_id = $this->parent->get_user_id();
        
        $laika_attempt = new LaikaAttempt();
        $laika_attempt->set_user_id($user_id);
        $laika_attempt->create();
        
        foreach ($question_answers as $question => $answer)
        {
            $laika_answer = new LaikaAnswer();
            $laika_answer->set_user_id($user_id);
            $laika_answer->set_attempt_id($laika_attempt->get_id());
            $laika_answer->set_question_id($question);
            $laika_answer->set_answer($answer);
            $laika_answer->create();
        }
        
        // Retrieve all scales
        $ldm = LaikaDataManager :: get_instance();
        
        $scales = $ldm->retrieve_laika_scales();
        
        while ($scale = $scales->next_result())
        {
            $scale_question_condition = new EqualityCondition(LaikaQuestion :: PROPERTY_SCALE_ID, $scale->get_id());
            $scale_questions = $ldm->retrieve_laika_questions($scale_question_condition);
            
            $result = 0;
            
            while ($scale_question = $scale_questions->next_result())
            {
                $question_id = $scale_question->get_id();
                //$weight = $scale_question->get_weight();
                $correction = $scale_question->get_correction();
                
                if ($correction > 0)
                {
                    $result += ($correction - $question_answers[$question_id]);
                }
                else
                {
                    $result += $question_answers[$question_id];
                }
            }
            
            $result_conditions = array();
            $result_conditions[] = new EqualityCondition(LaikaResult :: PROPERTY_SCALE_ID, $scale->get_id());
            $result_conditions[] = new EqualityCondition(LaikaResult :: PROPERTY_RESULT, $result);
            $result_condition = new AndCondition($result_conditions);
            
            $laika_result = $ldm->retrieve_laika_results($result_condition, null, 1);
            $laika_result = $laika_result->next_result();
            
            $laika_calculated_result = new LaikaCalculatedResult();
            $laika_calculated_result->set_attempt_id($laika_attempt->get_id());
            $laika_calculated_result->set_scale_id($laika_result->get_scale_id());
            $laika_calculated_result->set_result($laika_result->get_result());
            $laika_calculated_result->set_percentile($laika_result->get_percentile());
            $laika_calculated_result->set_percentile_code($laika_result->get_percentile_code());
            $laika_calculated_result->create();
        }
        
        $page->controller->container(true);
        $this->parent->redirect(Translation :: get('LaikaSaved'), false, array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME));
    }
}
?>