<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Utilities;

/**
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array('survey_select_question' => 'survey_select_question');
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