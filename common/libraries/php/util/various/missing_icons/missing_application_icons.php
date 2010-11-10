<?php
namespace common\libraries;

use application\weblcms\Tool;
use repository\RepositoryDataManager;
use repository\ContentObject;

function check_icons($applications)
{
    $sizes = array(Theme :: ICON_MINI, Theme :: ICON_SMALL, Theme :: ICON_MEDIUM, Theme :: ICON_BIG);

    $html = array();
    foreach ($applications as $application)
    {
        $failures = 0;
        foreach ($sizes as $size)
        {
            $icon_paths = array();
            $icon_paths[] = Theme :: get_image_system_path(Application :: determine_namespace($application)) . 'logo/' . $size . '.png';

            foreach ($icon_paths as $icon_path)
            {
                if (! file_exists($icon_path))
                {
                    $failures ++;
                    $html[] = $application . ' - ' . $icon_path;
                }
            }
        }

        if ($failures > 0)
        {
            $html[] = '';
        }
    }

    echo implode("<br />\n", $html);
}

$core_applications = array('webservice', 'admin', 'help', 'reporting', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu', 'migration');
$web_applications = Filesystem :: get_directory_content(Path :: get_application_path(), Filesystem :: LIST_DIRECTORIES, false);

check_icons($core_applications);
check_icons($web_applications);
?>