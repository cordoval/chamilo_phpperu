<?php
/**
 * $Id: questions_laika_wizard_page.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/laika_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class QuestionsLaikaWizardPage extends LaikaWizardPage
{
    private $counter;

    public function QuestionsLaikaWizardPage($name, $parent, $counter)
    {
        parent :: LaikaWizardPage($name, $parent);
        $this->counter = $counter;
    }

    function buildForm()
    {
        $ldm = LaikaDataManager :: get_instance();
        $counter = $this->counter;
        $i = 1 + $counter;
        
        $questions = $ldm->retrieve_laika_questions(null, $counter, 10);
        
        $this->addElement('html', '<div style="width: 900px;">');
        $this->addElement('html', '<div style="padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #c0c0c0;"><div style="float: left; width: 300px;"></div><div style="float: right; width: 500px;"><div style="float:left; width: 20%; text-align: center;">' . Translation :: get('NotTypical') . '</div><div style="float:left; width: 20%; text-align: center;">' . Translation :: get('NotVeryTypical') . '</div><div style="float:left; width: 20%; text-align: center;">' . Translation :: get('SomewhatTypical') . '</div><div style="float:left; width: 20%; text-align: center;">' . Translation :: get('FairlyTypical') . '</div><div style="float:left; width: 20%; text-align: center;">' . Translation :: get('VeryTypical') . '</div><div class="clear"></div></div><div class="clear"></div></div>');
        
        while ($question = $questions->next_result())
        {
            $group = array();
            
            $renderer = $this->defaultRenderer();
            
            $group[] = & $this->createElement('radio', 'question[' . $question->get_id() . ']', null, null, 1);
            $group[] = & $this->createElement('radio', 'question[' . $question->get_id() . ']', null, null, 2);
            $group[] = & $this->createElement('radio', 'question[' . $question->get_id() . ']', null, null, 3);
            $group[] = & $this->createElement('radio', 'question[' . $question->get_id() . ']', null, null, 4);
            $group[] = & $this->createElement('radio', 'question[' . $question->get_id() . ']', null, null, 5);
            
            $qf_question = $this->addGroup($group, 'question[' . $question->get_id() . ']', $i . ') ' . $question->get_title(), '', false);
            $this->addRule('question[' . $question->get_id() . ']', Translation :: get('ThisFieldIsRequired'), 'required');
            $renderer->setElementTemplate('<div style="padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #c0c0c0;"><div style="float: left; width: 300px;">{label}</div><div style="float: right; width: 500px;">{element}<div class="clear"></div></div><div class="clear"></div></div>', 'question[' . $question->get_id() . ']');
            $renderer->setGroupElementTemplate('<div style="float:left; width: 20%; text-align: center;">{element}</div>', 'question[' . $question->get_id() . ']');
            
            $i ++;
        }
        
        $this->addElement('html', '</div>');
        
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous'));
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }
}
?>