<?php
namespace repository\content_object\assessment_matrix_question;

use common\libraries\Request;

/**
 * @package repository.content_object.assessment_matrix_question;
 */
require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

$value = Request :: post('value');
$action = Request :: post('action');

switch ($action)
{
    case 'skip_option' :
        $_SESSION['mq_skip_options'][] = $value;
        break;
    case 'skip_match' :
        $_SESSION['mq_skip_matches'][] = $value;
        break;
}
?>