<?php
namespace repository\content_object\survey_rating_question;

use common\libraries\Translation;
use common\libraries\Path;

/**
 * $Id: survey_rating_question_form.class.php $
 * @package repository.lib.content_object.survey_rating_question
 */
require_once Path :: get_repository_path() . '/question_types/rating_question/rating_question_form.class.php';

/**
 * This class represents a form to create or update open questions
 */
class SurveyRatingQuestionForm extends RatingQuestionForm
{

    function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));

        $elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Percentage') . ' (0-100)', 0, array('onclick' => 'javascript:hide_controls(\'buttons\')'));
        $elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Rating'), 1, array('onclick' => 'javascript:show_controls(\'buttons\')'));
        $this->addGroup($elem, 'type', Translation :: get('SurveyRatingType'), '<br />', false);

        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="buttons">');
        $ratings[] = $this->createElement('text', RatingQuestion :: PROPERTY_LOW, null, array('class' => 'rating_question_low_value', 'style' => 'width: 124px; margin-right: 4px;'));
        $ratings[] = $this->createElement('text', RatingQuestion :: PROPERTY_HIGH, null, array('class' => 'rating_question_high_value', 'style' => 'width: 124px;'));
        $this->addGroup($ratings, 'ratings', null, '', false);
        $this->addElement('html', '</div>');

        $this->addElement('html', "<script type=\"text/javascript\">
			/* <![CDATA[ */
			hide_controls('buttons');
			function show_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='';
			}
			function hide_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='none';
			}
			/* ]]> */
				</script>\n");
        $this->addElement('category');

        $this->addGroupRule('ratings', array(RatingQuestion :: PROPERTY_LOW => array(array(Translation :: get('ValueShouldBeNumeric'), 'numeric')), RatingQuestion :: PROPERTY_HIGH => array(array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));

        $elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Percentage'), 0, array('onclick' => 'javascript:hide_controls(\'buttons\')', 'id' => 'ratingtype_percentage'));
        $elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Rating'), 1, array('onclick' => 'javascript:show_controls(\'buttons\')'));
        $this->addGroup($elem, 'type', Translation :: get('SurveyRatingType'), '<br />', false);

        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="buttons">');
        $ratings[] = $this->createElement('text', RatingQuestion :: PROPERTY_LOW, null, array('class' => 'rating_question_low_value', 'style' => 'width: 124px; margin-right: 4px;'));
        $ratings[] = $this->createElement('text', RatingQuestion :: PROPERTY_HIGH, null, array('class' => 'rating_question_high_value', 'style' => 'width: 124px;'));
        $this->addGroup($ratings, 'ratings', null, '', false);
        $this->addElement('html', '</div>');

        $this->addElement('html', "<script type=\"text/javascript\">
			/* <![CDATA[ */
			var ratingtype_percentage = document.getElementById('ratingtype_percentage');
			if (ratingtype_percentage.checked)
			{
				hide_controls('buttons');
			}
			function show_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='';
			}
			function hide_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='none';
			}
			/* ]]> */
				</script>\n");
        $this->addElement('category');

        $this->addGroupRule('ratings', array(RatingQuestion :: PROPERTY_LOW => array(array(Translation :: get('ValueShouldBeNumeric'), 'numeric')), RatingQuestion :: PROPERTY_HIGH => array(array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));
    }

    function create_content_object()
    {
        $values = $this->exportValues();
        $object = new SurveyRatingQuestion();

        if (isset($values[RatingQuestion :: PROPERTY_LOW]) && $values[RatingQuestion :: PROPERTY_LOW] != '')
            $object->set_low($values[RatingQuestion :: PROPERTY_LOW]);
        else
            $object->set_low(0);

        if (isset($values[RatingQuestion :: PROPERTY_HIGH]) && $values[RatingQuestion :: PROPERTY_HIGH] != '')
            $object->set_high($values[RatingQuestion :: PROPERTY_HIGH]);
        else
            $object->set_high(100);

        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        $object = parent :: get_content_object();

        if (isset($values[RatingQuestion :: PROPERTY_LOW]) && $values[RatingQuestion :: PROPERTY_LOW] != '')
            $object->set_low($values[RatingQuestion :: PROPERTY_LOW]);
        else
            $object->set_low(0);

        if (isset($values[RatingQuestion :: PROPERTY_HIGH]) && $values[RatingQuestion :: PROPERTY_HIGH] != '')
            $object->set_high($values[RatingQuestion :: PROPERTY_HIGH]);
        else
            $object->set_high(100);

        $this->set_content_object($object);
        return parent :: update_content_object();
    }
}
?>