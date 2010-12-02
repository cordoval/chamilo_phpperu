<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Path;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/../../../global.inc.php';

header("Content-type: text/css");

$html = array();
$html[] = '@CHARSET "ISO-8859-1";';

$video_conferencing_manager_types = VideoConferencingManager :: get_registered_types();

while ($video_conferencing_manager_type = $video_conferencing_manager_types->next_result())
{
    $html[] = '@import url("../implementation/' . $video_conferencing_manager_type->get_name() . '/resources/css/' . Theme :: get_theme() . '/' . Theme :: get_theme() . '.css");';
//    $html[] = '';
}

echo implode("\n", $html);
?>