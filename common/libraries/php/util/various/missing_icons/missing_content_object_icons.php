<?php
namespace common\libraries;

use repository\RepositoryDataManager;
use repository\ContentObject;

$sizes = array(Theme :: ICON_MINI, Theme :: ICON_SMALL, Theme :: ICON_BIG);
$content_objects = RepositoryDataManager :: get_registered_types();

$data = array();
foreach ($content_objects as $content_object)
{
    $data_row = array();
    $data_row[] = $content_object;
    foreach ($sizes as $size)
    {
        $icon_path = Theme :: get_image_system_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '.png';
        if (! file_exists($icon_path))
        {
            $data_row[] = '<img src="' . Theme :: get_common_image_path() . 'error/' . $size . '.png" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '.png" />';
        }

        if ($size == Theme :: ICON_SMALL || $size == Theme :: ICON_MINI)
        {
            $icon_path = Theme :: get_image_system_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '_na.png';
            if (! file_exists($icon_path))
            {
                $data_row[] = '<img src="' . Theme :: get_common_image_path() . 'error/' . $size . '.png" />';
            }
            else
            {
                $data_row[] = '<img src="' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '_na.png" />';
            }

        //$icon_paths[] = Theme :: get_image_system_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '_na.png';
        }

        if ($size == Theme :: ICON_SMALL)
        {
            $icon_path = Theme :: get_image_system_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '_new.png';
            if (! file_exists($icon_path))
            {
                $data_row[] = '<img src="' . Theme :: get_common_image_path() . 'error/' . $size . '.png" />';
            }
            else
            {
                $data_row[] = '<img src="' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '_new.png" />';
            }

        //$icon_paths[] = Theme :: get_image_system_path(ContentObject :: get_content_object_type_namespace($content_object)) . 'logo/' . $size . '_new.png';
        }
    }

    $data[] = $data_row;
}

$table = new SortableTableFromArray($data, 0, 200);
$table->set_header(0, 'Content Object');

$table->set_header(1, '16 x 16');
$table->set_header(2, '16 x 16 NA');

$table->set_header(3, '32 x 32');
$table->set_header(4, '32 x 32 NA');
$table->set_header(5, '32 x 32 NEW');

$table->set_header(6, '48 x 48');

echo $table->as_html();
?>