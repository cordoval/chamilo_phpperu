<?php

namespace common\libraries;

/**
 * $Id: match_question.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.javascript.ajax
 */
require_once dirname(__FILE__) . '/../../../global.inc.php';

$value = Request :: post('value');
$action = Request :: post('action');

switch ($action)
{
    case 'skip_match' :
        $_SESSION['match_skip_options'][] = $value;
}
?>