<?php
namespace repository\content_object\assessment;

use common\libraries\Utilities;

/**
 * $Id: question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc
 */
abstract class QuestionResultDisplay
{
    private $complex_content_object_question;
    private $question;
    private $question_nr;
    private $answers;
    private $score;

    function __construct($complex_content_object_question, $question_nr, $answers, $score)
    {
        $this->complex_content_object_question = $complex_content_object_question;
        $this->question_nr = $question_nr;
        $this->question = $complex_content_object_question->get_ref();
        $this->answers = $answers;
        $this->score = $score;
    }

    function get_complex_content_object_question()
    {
        return $this->complex_content_object_question;
    }

    function get_question()
    {
        return $this->question;
    }

    function get_question_nr()
    {
        return $this->question_nr;
    }

    function get_answers()
    {
        return $this->answers;
    }

    function get_score()
    {
        return $this->score;
    }

    function as_html()
    {
        $html = array();

        $html[] = $this->header();

        if ($this->add_borders())
        {
            $html[] = '<div class="with_borders">';
        }

        $html[] = $this->get_question_result();

        if ($this->add_borders())
        {
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        $html[] = $this->footer();
        return implode("\n", $html);
    }

    function display()
    {
        echo $this->as_html();
    }

    function get_question_result()
    {
        return $this->get_score() . '<br />';
    }

    function header()
    {
        $html = array();

        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
        $html[] = '<div class="bevel">';
        $html[] = $this->question_nr . '.';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';

        $html[] = '<div class="bevel" style="float: left;">';
        $html[] = $this->question->get_title();
        $html[] = '</div>';
        $html[] = '<div class="bevel" style="text-align: right;">';
        $html[] = $this->get_score() . ' / ' . $this->get_complex_content_object_question()->get_weight();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="answer">';

        $description = $this->question->get_description();
        if ($this->question->has_description())
        {
            $html[] = '<div class="description">';
            $html[] = $description;
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        $html[] = '<div class="clear"></div>';

        return implode("\n", $html);
    }

    function footer()
    {
        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    function add_borders()
    {
        return false;
    }

    static function factory($complex_content_object_question, $question_nr, $answers, $score)
    {
        $type = $complex_content_object_question->get_ref()->get_type();

        $file = dirname(__FILE__) . '/question_result_display/' . $type . '_result_display.class.php';

        if (! file_exists($file))
        {
            die('file does not exist: ' . $file);
        }

        require_once $file;

        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'ResultDisplay';
        $question_result_display = new $class($complex_content_object_question, $question_nr, $answers, $score);
        return $question_result_display;
    }
}
?>