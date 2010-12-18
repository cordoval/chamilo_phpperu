<?php
namespace repository\content_object\survey_description;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * @package repository.content_object.survey_description
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * A SurveyDescription
 */
class SurveyDescription extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}

?>