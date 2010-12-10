<?php
namespace repository\content_object\assessment_multiple_choice_question;

use common\libraries\Request;

/**
 * @package repository.content_object.assessment_multiple_choice_question;
 */
require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

$value = Request :: post('value');
$action = Request :: post('action');

switch ($action)
{
    case 'skip_option' :
        $_SESSION['mc_skip_options'][] = $value;
}
?>