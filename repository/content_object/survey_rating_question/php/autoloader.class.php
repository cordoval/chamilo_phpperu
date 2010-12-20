<?php
namespace repository\content_object\survey_rating_question;

use common\libraries\Utilities;

/**
 * @package repository.content_object.survey_rating_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array('survey_rating_question' => 'survey_rating_question');
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/' . $url . '.class.php';
            return true;
        }

        return false;
    }

}

?>