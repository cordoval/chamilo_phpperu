<?php
require_once dirname(__FILE__) . '/../../../../peer_assessment/component/viewer/wizard/inc/peer_assessment_question_display.class.php';

class QuestionsPeerAssessmentViewerWizardPage extends PeerAssessmentViewerWizardPage
{
    private $page_number;
    private $questions;

    function QuestionsPeerAssessmentViewerWizardPage($name, $parent, $page_number)
    {
        parent :: PeerAssessmentViewerWizardPage($name, $parent);
        $this->page_number = $page_number;
        $this->addAction('process', new PeerAssessmentViewerWizardProcess($this));

    }

    function buildForm()
    {
        $this->_formBuilt = true;

        $this->questions = $this->get_parent()->get_questions($this->page_number);

        $question_count = count($this->questions);

        $peer_assessment_page = $this->get_parent()->get_page($this->page_number);

        // Add buttons next, back and submit
        if ($this->page_number > 1)
        {
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Back'), array('class' => 'previous'));
        }

        if ($this->page_number < $this->get_parent()->get_total_pages())
        {      	
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'next'));
        }
        else
        {
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit'), array('class' => 'positive'));
        }
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        
        // Add question forms
        if ($question_count != 0)
        {
            foreach ($this->questions as $nr => $question)
            {
                $question_display = PeerAssessmentQuestionDisplay :: factory($this, $question, $nr, $this->get_parent()->get_peer_assessment(), $this->page_number);
                $question_display->display();
            }
        }

        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
        $this->setDefaultAction('next');
    }

    function get_page_number()
    {
        return $this->page_number;
    }
}
?>