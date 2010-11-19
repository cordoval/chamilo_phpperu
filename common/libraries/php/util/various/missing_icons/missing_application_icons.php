<?php
namespace common\libraries;

use application\weblcms\Tool;
use repository\RepositoryDataManager;
use repository\ContentObject;

function check_icons($applications)
{
    $sizes = array(Theme :: ICON_MINI, Theme :: ICON_SMALL, Theme :: ICON_MEDIUM, Theme :: ICON_BIG);

    $failures = 0;
    $data = array();

    foreach ($applications as $application)
    {
        $data_row = array();
        $data_row[] = $application;

        foreach ($sizes as $size)
        {
            $icon_path = Theme :: get_image_system_path(Application :: determine_namespace($application)) . 'logo/' . $size . '.png';

            if (! file_exists($icon_path))
            {
                $failures++;
                $data_row[] = '<img src="' . Theme :: get_common_image_path() . 'error/' . $size . '.png" />';
            }
            else
            {
                $data_row[] = '<img src="' . Theme :: get_image_path(Application :: determine_namespace($application)) . 'logo/' . $size . '.png" />';
            }
        }

        $data[] = $data_row;
    }

    $table = new SortableTableFromArray($data, 0, 200);
    $table->set_header(0, 'Application');

    foreach ($sizes as $key => $size)
    {
        $table->set_header($key + 1, $size . ' x ' . $size);
    }

    echo $table->as_html();

    echo '<b>Missing icons: ' . $failures . '</b>';
}

$core_applications = array('webservice', 'admin', 'help', 'reporting', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu', 'migration');
$web_applications = Filesystem :: get_directory_content(Path :: get_application_path(), Filesystem :: LIST_DIRECTORIES, false);

check_icons($core_applications);
check_icons($web_applications);
?>