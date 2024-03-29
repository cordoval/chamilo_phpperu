<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Path;
use common\libraries\Toolbar;

use common\extensions\repo_viewer\ContentObjectTableCellRenderer;
/**
 * $Id: export_content_object_table_cell_renderer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once Path :: get_common_extensions_path() . 'repo_viewer/php/component/content_object_table/content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/export_content_object_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class ExportContentObjectTableCellRenderer extends ContentObjectTableCellRenderer
{

    function get_publish_links($content_object)
    {
        $toolbar = new Toolbar();

        foreach ($this->get_table_actions() as $table_action)
        {
            $action = clone $table_action;
            $action->set_href(str_replace('%d', $content_object->get_id(), $table_action->get_href()));
        }
        $toolbar->add_item($action);

        return $toolbar->as_html();
    }
}
?>