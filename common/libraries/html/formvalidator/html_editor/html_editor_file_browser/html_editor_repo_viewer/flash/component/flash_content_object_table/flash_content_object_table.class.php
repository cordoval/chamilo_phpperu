<?php
/**
 * $Id: content_object_table.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once dirname(__FILE__) . '/flash_content_object_table_data_provider.class.php';
require_once dirname(__FILE__) . '/flash_content_object_table_column_model.class.php';
require_once dirname(__FILE__) . '/flash_content_object_table_cell_renderer.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class FlashContentObjectTable extends ObjectTable
{
    const DEFAULT_NAME = 'flash_content_object_table';

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
    function FlashContentObjectTable($parent, $owner, $types, $query, $table_actions)
    {
        $data_provider = new FlashContentObjectTableDataProvider($owner, $types, $query, $parent);
        $column_model = new FlashContentObjectTableColumnModel();
        $cell_renderer = new FlashContentObjectTableCellRenderer($table_actions);
        parent :: __construct($data_provider, FlashContentObjectTable :: DEFAULT_NAME, $column_model, $cell_renderer);

        if ($parent->get_maximum_select() != RepoViewer :: SELECT_SINGLE)
        {
            $actions = array();
            $actions[] = new ObjectTableFormAction(RepoViewer :: PARAM_PUBLISH_SELECTED, Translation :: get('PublishSelected'), false);
        }

        $this->set_form_actions($actions);
    }
}
?>