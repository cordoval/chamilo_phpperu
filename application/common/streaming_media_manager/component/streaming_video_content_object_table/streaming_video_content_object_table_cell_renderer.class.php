<?php
/**
 * $Id: streaming_video_content_object_table_cell_renderer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/content_object_table/content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/streaming_video_content_object_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class StreamingVideoContentObjectTableCellRenderer extends ContentObjectTableCellRenderer
{
    function get_publish_links($content_object)
    {
        $toolbar_data = array();
        $table_actions = $this->get_table_actions();

        foreach ($table_actions as $table_action)
        {
            $table_action['href'] = str_replace('%d', $content_object->get_id(), $table_action['href']);
            $toolbar_data[] = $table_action;
        }

        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>