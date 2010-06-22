<?php
/**
 * $Id: hotspot_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.hotspot_question
 */
require_once dirname(__FILE__) . '/hotspot_question_answer.class.php';
/**
 * This class represents a hotspot question
 */
class HotspotQuestion extends ContentObject implements Versionable
{
    const PROPERTY_ANSWERS = 'answers';
    const PROPERTY_IMAGE = 'image';

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    public function supports_attachments()
    {
    	return false;
    }

    public function add_answer($answer)
    {
        $answers = $this->get_answers();
        $answers[] = $answer;
        return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
    }

    public function set_answers($answers)
    {
        return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
    }

    public function get_answers()
    {
        if ($result = unserialize($this->get_additional_property(self :: PROPERTY_ANSWERS)))
        {
            return $result;
        }
        return array();
    }

    public function get_number_of_answers()
    {
        return count($this->get_answers());
    }

    public function get_image()
    {
        return $this->get_additional_property(self :: PROPERTY_IMAGE);
    }

    public function set_image($image)
    {
        $this->set_additional_property(self :: PROPERTY_IMAGE, $image);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ANSWERS, self :: PROPERTY_IMAGE);
    }

    function get_image_object()
    {
        $image = $this->get_image();

        if (isset($image) && $image != 0)
        {
            return RepositoryDataManager :: get_instance()->retrieve_content_object($image);
        }
        else
        {
            return null;
        }
    }
}
?>