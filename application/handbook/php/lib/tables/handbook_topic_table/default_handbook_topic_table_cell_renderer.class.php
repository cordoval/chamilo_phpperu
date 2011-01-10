<?php
namespace application\handbook;
use common\libraries\ObjectTableCellRenderer;
use common\libraries\Theme;
use repository\content_object\handbook\Handbook;
use repository\ContentObject;


class DefaultHandbookTopicTableCellRenderer extends ObjectTableCellRenderer
{

    
    function __construct($browser)
    {
    
    }

       function render_cell($column, $handbook)
    {
         switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE:
                return  $handbook->get_title();
            case ContentObject::PROPERTY_DESCRIPTION:
                return $handbook->get_description();
        }
        
        $title = $column->get_title();
        if ($title == '')
        {
            $img = Theme :: get_image_path(ContentObject::get_content_object_type_namespace('handbook')) . 'logo/22.png';
            return '<img src="' . $img . '"alt="course" />';
        }
        
        return '&nbsp;';
    }

    function render_id_cell($course)
    {
        return $course->get_id();
    }
}
?>