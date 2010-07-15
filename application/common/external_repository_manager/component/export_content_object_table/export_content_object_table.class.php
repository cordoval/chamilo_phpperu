<?php
/**
 * $Id: content_object_table.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once dirname(__FILE__) . '/export_content_object_table_data_provider.class.php';
require_once dirname(__FILE__) . '/export_content_object_table_column_model.class.php';
require_once dirname(__FILE__) . '/export_content_object_table_cell_renderer.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class ExportContentObjectTable extends ObjectTable
{
    const DEFAULT_NAME = 'video_content_object_table';

    /**
     * Constructor.
     * @param int $owner The id of the current user.
     * @param array $types The types of objects that can be published in current
     * location.
     * @param string $query The search query, or null if none.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     * @see PublicationCandidateTableCellRenderer::PublicationCandidateTableCellRenderer()
     */
    function ExportContentObjectTable($parent, $owner, $types, $query, $table_actions)
    {
        $data_provider = new ExportContentObjectTableDataProvider($owner, $types, $query, $parent);
        $column_model = new ExportContentObjectTableColumnModel();
        $cell_renderer = new ExportContentObjectTableCellRenderer($table_actions);
        parent :: __construct($data_provider, ExportContentObjectTable :: DEFAULT_NAME, $column_model, $cell_renderer);

        $this->set_form_actions($actions);
    }
}
?>