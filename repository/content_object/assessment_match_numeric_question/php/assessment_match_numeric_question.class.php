<?php
namespace repository\content_object\assessment_match_numeric_question;

/**
  * @package repository.lib.content_object.match_numeric_question
 */
require_once dirname(__FILE__) . '/main.php';

class AssessmentMatchNumericQuestion extends ContentObject implements Versionable
{
	const CLASS_NAME = __CLASS__;

    const PROPERTY_OPTIONS = 'options';
	const PROPERTY_TOLERANCE_TYPE = 'tolerance_type';

	const TOLERANCE_TYPE_ABSOLUTE = 'absolute';
	const TOLERANCE_TYPE_RELATIVE = 'relative';

	public static function get_type_name() {
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    public static function get_additional_property_names(){
    	$result = array();
    	$result[] = self :: PROPERTY_TOLERANCE_TYPE;
    	$result[] = self :: PROPERTY_OPTIONS;
    	return $result;
    }

    public function ContentObject($defaultProperties = array (), $additionalProperties = null){
        parent :: __construct($defaultProperties, $additionalProperties);
    	if(!isset($additionalProperties[self::PROPERTY_TOLERANCE_TYPE])){
        	$this->set_tolerance_type(self::TOLERANCE_TYPE_ABSOLUTE);
    	}
    }

    public function add_option($option){
        $options = $this->get_options();
        $options[] = $option;
        return $this->set_additional_property(self::PROPERTY_OPTIONS, serialize($options));
    }

    public function set_options($options){
        return $this->set_additional_property(self::PROPERTY_OPTIONS, serialize($options));
    }

    public function get_options(){
        if ($result = unserialize($this->get_additional_property(self::PROPERTY_OPTIONS))){
            return $result;
        }
        return array();
    }

    public function get_number_of_options(){
        return count($this->get_options());
    }

    public function set_tolerance_type($type){
        return $this->set_additional_property(self::PROPERTY_TOLERANCE_TYPE, $type);
    }

    public function get_tolerance_type(){
        return $this->get_additional_property(self::PROPERTY_TOLERANCE_TYPE);
    }
}
