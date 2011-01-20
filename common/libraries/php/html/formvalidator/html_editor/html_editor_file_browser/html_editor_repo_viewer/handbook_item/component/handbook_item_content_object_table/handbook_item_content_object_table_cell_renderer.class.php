<?php
namespace common\libraries;

use common\extensions\repo_viewer\ContentObjectTableCellRenderer;
use repository\ContentObject;
use common\extensions\repo_viewer\ContentObjectTableColumnModel;

/**
 * $Id: handbook_item_content_object_table_cell_renderer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once Path :: get_common_extensions_path() . 'repo_viewer/php/component/content_object_table/content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/handbook_item_content_object_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class HandbookItemContentObjectTableCellRenderer extends ContentObjectTableCellRenderer
{

     /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_columns()
    {
    
    }

    function render_cell($column, $content_object)
    {
        $rdm = \repository\RepositoryDataManager::get_instance();
        $co_id = $content_object->get_reference();
        $co = $rdm->retrieve_content_object($co_id);
       
         switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_ID :
                return $content_object->get_id();
            case ContentObject :: PROPERTY_TYPE :
                $type = $co->get_type();
                $icon = $content_object->get_icon_name();
                return '<img src="' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($type)) . 'logo/' . $icon . '.png" alt="' . htmlentities(Translation :: get('TypeName', null, ContentObject :: get_content_object_type_namespace($type))) . '"/>';
            case ContentObject :: PROPERTY_TITLE :
                return Utilities :: truncate_string($co->get_title(), 50);
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($co->get_description(), 50);
           case Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES):
//                return 'test2';
              return $this->get_publish_links($content_object);
            default :
                return '&nbsp;';
        }
    }
}
?>