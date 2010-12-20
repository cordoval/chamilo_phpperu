<?php
namespace repository\content_object\survey_open_question;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an open question
 */
class SurveyOpenQuestion extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>