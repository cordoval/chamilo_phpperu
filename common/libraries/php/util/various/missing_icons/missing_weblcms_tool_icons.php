<?php
namespace common\libraries;

use application\weblcms\Tool;
use repository\RepositoryDataManager;
use repository\ContentObject;

$sizes = array(Theme :: ICON_MINI, Theme :: ICON_MEDIUM);
$tools = Filesystem :: get_directory_content(WebApplication :: get_application_path('weblcms') . 'tool/', Filesystem :: LIST_DIRECTORIES, false);

$failures = 0;
$data = array();
foreach ($tools as $tool)
{
    $data_row = array();
    $data_row[] = $tool;
    foreach ($sizes as $size)
    {
        $icon_path = Theme :: get_image_system_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '.png';
        if (! file_exists($icon_path))
        {
            $failures++;
            $data_row[] = '<img src="' . Theme :: get_common_image_path() . 'error/' . $size . '.png" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme :: get_image_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '.png" />';
        }

        $icon_path = Theme :: get_image_system_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '_na.png';
        if (! file_exists($icon_path))
        {
            $failures++;
            $data_row[] = '<img src="' . Theme :: get_common_image_path() . 'error/' . $size . '.png" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme :: get_image_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '_na.png" />';
        }

        $icon_path = Theme :: get_image_system_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '_new.png';
        if (! file_exists($icon_path))
        {
            $failures++;
            $data_row[] = '<img src="' . Theme :: get_common_image_path() . 'error/' . $size . '.png" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme :: get_image_path(Tool :: get_tool_type_namespace($tool)) . 'logo/' . $size . '_new.png" />';
        }
    }

    $data[] = $data_row;
}

$table = new SortableTableFromArray($data, 0, 200);
$table->set_header(0, 'Tool');

foreach ($sizes as $key => $size)
{
    $table->set_header(($key * 3) + 1, $size . ' x ' . $size);
    $table->set_header(($key * 3) + 2, $size . ' x ' . $size . ' NA');
    $table->set_header(($key * 3) + 3, $size . ' x ' . $size . ' NEW');
}

echo $table->as_html();

echo '<b>Missing icons: ' . $failures . '</b>';
?>