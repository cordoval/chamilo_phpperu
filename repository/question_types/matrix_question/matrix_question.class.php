<?php
/**
 * $Id: matrix_question.class.php
 * @package repository.question_types.matrix_question
 */
require_once dirname(__FILE__) . '/matrix_question_option.class.php';

abstract class MatrixQuestion extends ContentObject
{
	const PROPERTY_OPTIONS = 'options';
    const PROPERTY_MATCHES = 'matches';
    const PROPERTY_MATRIX_TYPE = 'matrix_type';
    
    const MATRIX_TYPE_RADIO = 1;
    const MATRIX_TYPE_CHECKBOX = 2;
    
    public function add_option($option)
    {
        $options = $this->get_options();
        $options[] = $option;
        return $this->set_additional_property(self :: PROPERTY_OPTIONS, serialize($options));
    }

    public function set_options($options)
    {
        return $this->set_additional_property(self :: PROPERTY_OPTIONS, serialize($options));
    }

    public function get_options()
    {
        if ($result = unserialize($this->get_additional_property(self :: PROPERTY_OPTIONS)))
        {
            return $result;
        }
        return array();
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    public function add_match($match)
    {
        $matches = $this->get_matches();
        $matches[] = $match;
        return $this->set_additional_property(self :: PROPERTY_MATCHES, serialize($matches));
    }

    public function set_matches($matches)
    {
        return $this->set_additional_property(self :: PROPERTY_MATCHES, serialize($matches));
    }

    public function get_matches()
    {
        if ($result = unserialize($this->get_additional_property(self :: PROPERTY_MATCHES)))
        {
            return $result;
        }
        return array();
    }

    public function get_number_of_matches()
    {
        return count($this->get_matches());
    }

    public function get_matrix_type()
    {
        return $this->get_additional_property(self :: PROPERTY_MATRIX_TYPE);
    }

    public function set_matrix_type($matrix_type)
    {
        $this->set_additional_property(self :: PROPERTY_MATRIX_TYPE, $matrix_type);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_MATCHES, self :: PROPERTY_OPTIONS, self :: PROPERTY_MATRIX_TYPE);
    }
}
?>