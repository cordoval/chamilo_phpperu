<?php
namespace repository;

use common\libraries\Path;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/../../common/global.inc.php';

header("Content-type: text/css; charset=iso-8859-1");
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 60) . ' GMT');

$html = array();
$html[] = '@CHARSET "ISO-8859-1";';

$content_object_types = RepositoryDataManager :: get_registered_types();

foreach ($content_object_types as $content_object_type)
{
    $html[] = '@import url("../content_object/' . $content_object_type . '/resources/css/' . Theme :: get_theme() . '/' . Theme :: get_theme() . '.css");';
    $html[] = '';
}

echo implode("\n", $html);
?>