<?php
namespace repository;

use common\libraries\Request;

use common\libraries\Path;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/../../common/global.inc.php';

header("Content-type: text/css; charset=iso-8859-1");
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 60) . ' GMT');

$start = Request :: get('start');
$content_object_types = RepositoryDataManager :: get_registered_types();

$html = array();
$html[] = '@CHARSET "ISO-8859-1";';

if (!is_null($start))
{
    $content_object_types = array_slice($content_object_types, (int) $start, 20);

    foreach ($content_object_types as $content_object_type)
    {
        $html[] = '@import url("../content_object/' . $content_object_type . '/resources/css/' . Theme :: get_theme() . '/' . Theme :: get_theme() . '.css");';
        $html[] = '';
    }
}
else
{
    $pages = ceil(count($content_object_types) / 20);

    $i = 0;
    while ($i < $pages)
    {
        $html[] = '@import url("css_content_object.inc.php?start='. ($i * 20) .'");';
        $html[] = '';
        $i++;
    }
}

echo implode("\n", $html);
?>