<?php
/**
 * $Id: rating_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.rating_question
 */
require_once dirname(__FILE__) . '/rating_question.class.php';
/**
 * This class represents a form to create or update open questions
 */
class RatingQuestionForm extends ContentObjectForm
{

    function build_creation_form()
    {
        parent :: build_creation_form();
    }

    // Inherited
    function build_editing_form()
    {
        parent :: build_editing_form();
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        parent :: set_values($defaults);
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[RatingQuestion :: PROPERTY_LOW] = $object->get_low();
            $defaults[RatingQuestion :: PROPERTY_HIGH] = $object->get_high();
            
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
}
?>