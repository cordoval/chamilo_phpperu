<?php
namespace application\handbook;
use common\libraries\ObjectTableCellRenderer;
use common\libraries\Theme;
use repository\content_object\handbook\Handbook;
use repository\ContentObject;
use application\context_linker\ContextLink;

require_once dirname(__FILE__) . '/default_handbook_alternative_table_column_model.class.php';
class DefaultHandbookAlternativesTableCellRenderer extends ObjectTableCellRenderer
{

   
    
    function __construct($browser)
    {
    
    }

       function render_cell($column, $handbook)
    {
//        $url = $this->browser->get_view_handbook_publication_url($handbook->get_id());
        switch ($column->get_name())
        {
//            case Handbook::PROPERTY_TITLE:
//             return '<a href="' . $url . '" alt="' . $handbook->get_title() . '">' . $handbook->get_title() . '</a>';
//            case Handbook::PROPERTY_DESCRIPTION:
//                return '<a href="' . $url . '" alt="' . $handbook->get_description() . '">' . $handbook->get_description() . '</a>';
             case ContentObject::PROPERTY_TITLE:
                    return 'titel';
            case ContentObject::PROPERTY_DESCRIPTION:
                    return 'description';
            case ContentObject::PROPERTY_TYPE:
                    return 'type';
            case DefaultHandbookAlternativeTableColumnModel:: COLUMN_METADATA_PROPERTY_TYPE:
                    return 'metadataproperties'; 
            case DefaultHandbookAlternativeTableColumnModel :: COLUMN_METADATA_PROPERTY_VALUE:
                    return 'metadataproperties';

        }
        
//        $title = $column->get_title();
//        if ($title == '')
//        {
//            $img = Theme :: get_image_path(ContentObject::get_content_object_type_namespace('handbook')) . 'logo/22.png';
//            return '<img src="' . $img . '"alt="course" />';
//        }
        
        return '&nbsp;';
    }

    function render_id_cell($co)
    {
        return $co->get_id();
    }
}
?>