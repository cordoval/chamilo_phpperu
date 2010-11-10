<?php
namespace common\libraries;

use repository\RepositoryDataManager;
use repository\ContentObject;

$sizes = array(Theme :: ICON_MINI, Theme :: ICON_SMALL, /*Theme :: ICON_MEDIUM,*/
        Theme :: ICON_BIG/*, Theme :: ICON_HUGE*/);
$content_objects = RepositoryDataManager :: get_registered_types();

$html = array();
foreach ($content_objects as $content_object)
{
    $failures = 0;
    foreach ($sizes as $size)
    {
        $icon_paths = array();
        $icon_paths[] = Theme :: get_image_system_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '.png';

        if ($size == Theme :: ICON_SMALL || $size == Theme :: ICON_MINI)
        {
            $icon_paths[] = Theme :: get_image_system_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '_na.png';
        }

        if ($size == Theme :: ICON_SMALL)
        {
            $icon_paths[] = Theme :: get_image_system_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '_new.png';
        }

        foreach ($icon_paths as $icon_path)
        {
            if (! file_exists($icon_path))
            {
                $failures ++;
                $html[] = $content_object . ' - ' . $icon_path;
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