<?php
namespace repository\content_object\assessment_select_question;

use common\libraries\Request;

/**
 * @package repository.content_object.assessment_select_question;
 */

require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

$value = Request :: post('value');
$action = Request :: post('action');

switch ($action)
{
    case 'skip_option' :
        $_SESSION['select_skip_options'][] = $value;
}
?>