<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Path;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/../../../global.inc.php';

header("Content-type: text/css");

$html = array();
$html[] = '@CHARSET "ISO-8859-1";';

$external_repository_manager_types = ExternalRepositoryManager :: get_registered_types();

while ($external_repository_manager_type = $external_repository_manager_types->next_result())
{
    $html[] = '@import url("../implementation/' . $external_repository_manager_type->get_name() . '/resources/css/' . Theme :: get_theme() . '/' . Theme :: get_theme() . '.css");';
//    $html[] = '';
}

echo implode("\n", $html);
?>