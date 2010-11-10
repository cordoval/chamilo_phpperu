<?php
namespace common\libraries;

use application\weblcms\Tool;
use repository\RepositoryDataManager;
use repository\ContentObject;

$sizes = array(Theme :: ICON_MINI, Theme :: ICON_MEDIUM);
$tools = Filesystem :: get_directory_content(WebApplication :: get_application_path('weblcms') . 'tool/', Filesystem :: LIST_DIRECTORIES, false);

$html = array();
foreach ($tools as $tool)
{
    $failures = 0;
    foreach ($sizes as $size)
    {
        $icon_paths = array();
        $icon_paths[] = Theme :: get_image_system_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '.png';
        $icon_paths[] = Theme :: get_image_system_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '_na.png';
        $icon_paths[] = Theme :: get_image_system_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '_new.png';

        foreach ($icon_paths as $icon_path)
        {
            if (! file_exists($icon_path))
            {
                $failures ++;
                $html[] = $tool . ' - ' . $icon_path;
            }
        }
    }

    if ($failures > 0)
    {
        $html[] = '';
    }
}

echo implode("<br />\n", $html);
?>