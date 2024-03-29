<?php
namespace repository\content_object\assessment;

use common\libraries;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ResourceManager;
use common\libraries\Utilities;

/**
 * $Id: questions_assessment_viewer_wizard_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard
 */
require_once dirname(__FILE__) . '/inc/question_display.class.php';

class QuestionsAssessmentViewerWizardPage extends AssessmentViewerWizardPage
{
    private $page_number;
    private $questions;

    function __construct($name, $parent, $number)
    {
        parent :: __construct($name, $parent);
        $this->page_number = $number;
    }

    function get_number_of_questions()
    {
        return $this->questions->size();
    }

    function buildForm()
    {
        $this->_formBuilt = true;
        $this->questions = $this->get_parent()->get_questions($this->page_number);
        $assessment = $this->get_parent()->get_assessment();

        $values = $this->get_parent()->exportValues();

        $this->addElement('hidden', 'start_time', '', array('id' => 'start_time'));
        $this->addElement('hidden', 'max_time', '', array('id' => 'max_time'));
        $this->addElement('html', ResourceManager::get_instance()->get_resource_html(Path :: get_repository_content_object_path(true) . '/assessment/resources/javascript/assessment.js'));
        $defaults['start_time'] = ($values['start_time']) ? $values['start_time'] : 0;
        $defaults['max_time'] = ($assessment->get_maximum_time() * 60);
        $this->setConstants($defaults);

        if ($defaults['max_time'] > 0)
            $this->addElement('html', ' <div class="time_left">' . Translation :: get('TimeLeft') . '<br /><div class="time">' . $defaults['max_time'] . '</div>' . Translation :: get('SecondsShort') . '</div>');

        // Add buttons
        if ($this->page_number > 1)
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES), array('class' => 'previous'));

        if ($this->page_number < $this->get_parent()->get_total_pages())
        {
            $style = 'display: none';
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('process'), Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive finish process', 'style' => $style));
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), array('class' => 'next'));
        }

        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive finish', 'style' => $style));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        // Add question forms
        $i = (($this->page_number - 1) * $this->get_parent()->get_assessment()->get_questions_per_page()) + 1;

        while ($question = $this->questions->next_result())
        //foreach($this->questions as $question)
        {
            $question_display = QuestionDisplay :: factory($this, $question, $i);
            $question_display->display();
            $i ++;
        }

        // Add buttons
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
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