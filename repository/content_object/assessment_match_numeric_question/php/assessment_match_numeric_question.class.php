<?php
namespace repository\content_object\assessment_match_numeric_question;

use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\StringUtilities;
use repository\ContentObject;

/**
 * @package repository.lib.content_object.match_numeric_question
 */
require_once dirname(__FILE__) . '/main.php';

class AssessmentMatchNumericQuestion extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_TOLERANCE_TYPE = 'tolerance_type';
    const PROPERTY_HINT = 'hint';

    const TOLERANCE_TYPE_ABSOLUTE = 'absolute';
    const TOLERANCE_TYPE_RELATIVE = 'relative';

    public static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_TOLERANCE_TYPE, self :: PROPERTY_OPTIONS, self :: PROPERTY_HINT);
    }

    public function ContentObject($defaultProperties = array (), $additionalProperties = null)
    {
        parent :: __construct($defaultProperties, $additionalProperties);
        if (! isset($additionalProperties[self :: PROPERTY_TOLERANCE_TYPE]))
        {
            $this->set_tolerance_type(self :: TOLERANCE_TYPE_ABSOLUTE);
        }
    }

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

    /**
     * @return boolean
     */
    public function has_feedback()
    {
        foreach ($this->get_options() as $option)
        {
            if ($option->has_feedback())
            {
                return true;
            }
        }

        return false;
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    public function set_tolerance_type($type)
    {
        return $this->set_additional_property(self :: PROPERTY_TOLERANCE_TYPE, $type);
    }

    public function get_tolerance_type()
    {
        return $this->get_additional_property(self :: PROPERTY_TOLERANCE_TYPE);
    }

    public function set_hint($hint)
    {
        return $this->set_additional_property(self :: PROPERTY_HINT, $hint);
    }

    public function get_hint()
    {
        return $this->get_additional_property(self :: PROPERTY_HINT);
    }

    function has_hint()
    {
        return StringUtilities :: has_value($this->get_hint(), true);
    }
}
