<?php
namespace repository\content_object\assessment_rating_question;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;

use repository\ContentObjectForm;

/**
 * $Id: assessment_rating_question_form.class.php $
 * @package repository.lib.content_object.rating_question
 */

/**
 * This class represents a form to create or update open questions
 */
class AssessmentRatingQuestionForm extends ContentObjectForm
{

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[AssessmentRatingQuestion :: PROPERTY_HINT] = $object->get_hint();
            $defaults[AssessmentRatingQuestion :: PROPERTY_LOW] = $object->get_low();
            $defaults[AssessmentRatingQuestion :: PROPERTY_HIGH] = $object->get_high();
            $defaults[AssessmentRatingQuestion :: PROPERTY_CORRECT] = $object->get_correct();

            if ($object->get_low() == 0 && $object->get_high() == 100)
            {
                $defaults['ratingtype'] = 0;
            }
            else
            {
                $defaults['ratingtype'] = 1;
            }
        }
        else
        {
            $defaults['ratingtype'] = 0;
        }

        parent :: setDefaults($defaults);
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        $elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Percentage') . ' (0-100)', 0, array(
                'onclick' => 'javascript:hide_controls(\'buttons\')'));
        $elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Rating'), 1, array(
                'onclick' => 'javascript:show_controls(\'buttons\')'));
        $this->addGroup($elem, 'type', Translation :: get('Type', null, Utilities :: COMMON_LIBRARIES), '<br />', false);

        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="buttons">');
        $ratings[] = $this->createElement('text', AssessmentRatingQuestion :: PROPERTY_LOW, null, array(
                'class' => 'rating_question_low_value',
                'style' => 'width: 124px; margin-right: 4px;'));
        $ratings[] = $this->createElement('text', AssessmentRatingQuestion :: PROPERTY_HIGH, null, array(
                'class' => 'rating_question_high_value',
                'style' => 'width: 124px;'));
        $this->addGroup($ratings, 'ratings', null, '', false);
        $this->addElement('html', '</div>');

        $this->add_textfield(AssessmentRatingQuestion :: PROPERTY_CORRECT, Translation :: get('CorrectValue'), false);

        $html_editor_options = array();
        $html_editor_options['width'] = '595';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        $this->add_html_editor(AssessmentRatingQuestion :: PROPERTY_HINT, Translation :: get('Hint', array(), Utilities :: get_namespace_from_object($this)), false, $html_editor_options);

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

        $this->addGroupRule('ratings', array(
                AssessmentRatingQuestion :: PROPERTY_LOW => array(
                        array(
                                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                                'numeric')),
                AssessmentRatingQuestion :: PROPERTY_HIGH => array(
                        array(
                                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                                'numeric'))));

        $this->addRule(AssessmentRatingQuestion :: PROPERTY_CORRECT, Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES), 'numeric');
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));

        $elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Percentage'), 0, array(
                'onclick' => 'javascript:hide_controls(\'buttons\')',
                'id' => 'ratingtype_percentage'));
        $elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Rating'), 1, array(
                'onclick' => 'javascript:show_controls(\'buttons\')'));
        $this->addGroup($elem, 'type', Translation :: get('Type', null, Utilities :: COMMON_LIBRARIES), '<br />', false);

        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="buttons">');
        $ratings[] = $this->createElement('text', AssessmentRatingQuestion :: PROPERTY_LOW, null, array(
                'class' => 'rating_question_low_value',
                'style' => 'width: 124px; margin-right: 4px;'));
        $ratings[] = $this->createElement('text', AssessmentRatingQuestion :: PROPERTY_HIGH, null, array(
                'class' => 'rating_question_high_value',
                'style' => 'width: 124px;'));
        $this->addGroup($ratings, 'ratings', null, '', false);
        $this->addElement('html', '</div>');

        $this->add_textfield(AssessmentRatingQuestion :: PROPERTY_CORRECT, Translation :: get('CorrectValue'), false);

        $html_editor_options = array();
        $html_editor_options['width'] = '595';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        $this->add_html_editor(AssessmentRatingQuestion :: PROPERTY_HINT, Translation :: get('Hint', array(), Utilities :: get_namespace_from_object($this)), false, $html_editor_options);

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

        $this->addGroupRule('ratings', array(
                AssessmentRatingQuestion :: PROPERTY_LOW => array(
                        array(
                                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                                'numeric')),
                AssessmentRatingQuestion :: PROPERTY_HIGH => array(
                        array(
                                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                                'numeric'))));

        $this->addRule(AssessmentRatingQuestion :: PROPERTY_CORRECT, Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES), 'numeric');
    }

    function create_content_object($object)
    {
        $values = $this->exportValues();
        $object = new AssessmentRatingQuestion();
        $object->set_hint($values[AssessmentRatingQuestion :: PROPERTY_HINT]);

        if (isset($values[AssessmentRatingQuestion :: PROPERTY_LOW]) && $values[AssessmentRatingQuestion :: PROPERTY_LOW] != '')
        {
            $object->set_low($values[AssessmentRatingQuestion :: PROPERTY_LOW]);
        }
        else
        {
            $object->set_low(0);
        }

        if (isset($values[AssessmentRatingQuestion :: PROPERTY_HIGH]) && $values[AssessmentRatingQuestion :: PROPERTY_HIGH] != '')
        {
            $object->set_high($values[AssessmentRatingQuestion :: PROPERTY_HIGH]);
        }
        else
        {
            $object->set_high(100);
        }

        if (isset($values[AssessmentRatingQuestion :: PROPERTY_CORRECT]))
        {
            $object->set_correct($values[AssessmentRatingQuestion :: PROPERTY_CORRECT]);
        }

        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        $object = parent :: get_content_object();
        $object->set_hint($values[AssessmentRatingQuestion :: PROPERTY_HINT]);

        if (isset($values[AssessmentRatingQuestion :: PROPERTY_LOW]) && $values[AssessmentRatingQuestion :: PROPERTY_LOW] != '')
        {
            $object->set_low($values[AssessmentRatingQuestion :: PROPERTY_LOW]);
        }
        else
        {
            $object->set_low(0);
        }

        if (isset($values[AssessmentRatingQuestion :: PROPERTY_HIGH]) && $values[AssessmentRatingQuestion :: PROPERTY_HIGH] != '')
        {
            $object->set_high($values[AssessmentRatingQuestion :: PROPERTY_HIGH]);
        }
        else
        {
            $object->set_high(100);
        }

        if (isset($values[AssessmentRatingQuestion :: PROPERTY_CORRECT]))
        {
            $object->set_correct($values[AssessmentRatingQuestion :: PROPERTY_CORRECT]);
        }
        else
        {
            $object->set_correct(null);
        }

        $this->set_content_object($object);
        return parent :: update_content_object();
    }
}
?>