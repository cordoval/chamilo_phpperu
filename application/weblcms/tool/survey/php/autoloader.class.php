<?php
namespace application\weblcms\tool\survey;


use common\libraries\Utilities;

/**
 * $Id$
 * @author systho
 */
class Autoloader
{

    static function load($classname)
    {
        
        $list = array(
            'results_export' => 'component/assessment_results_export_form/export.class.php',
        );

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/' . $url;
            return true;
        }

        return false;
    }

}