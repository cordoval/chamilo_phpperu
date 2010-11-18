<?php
namespace application\handbook;
use common\libraries\ObjectTableCellRenderer;
use common\libraries\Theme;
use repository\content_object\handbook\Handbook;
use repository\ContentObject;


class DefaultHandbookPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    
    function DefaultHandbookPublicationTableCellRenderer($browser)
    {
    
    }

       function render_cell($column, $handbook)
    {
        $url = $this->browser->get_view_handbook_publication_url($handbook->get_id());
        switch ($column->get_name())
        {
            case Handbook::PROPERTY_TITLE:
             return '<a href="' . $url . '" alt="' . $handbook->get_title() . '">' . $handbook->get_title() . '</a>';
            case Handbook::PROPERTY_DESCRIPTION:
                return '<a href="' . $url . '" alt="' . $handbook->get_description() . '">' . $handbook->get_description() . '</a>';
             

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