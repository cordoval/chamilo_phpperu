<?php
namespace common\extensions\repo_viewer;
/**
 * $Id: content_object_table.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once dirname(__FILE__) . '/content_object_table_data_provider.class.php';
require_once dirname(__FILE__) . '/content_object_table_column_model.class.php';
require_once dirname(__FILE__) . '/content_object_table_cell_renderer.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class ContentObjectTable extends ObjectTable
{
    const DEFAULT_NAME = 'content_object_table';

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
    function ContentObjectTable($parent, $owner, $types, $query, $table_actions)
    {
        $data_provider = new ContentObjectTableDataProvider($owner, $types, $query, $parent);
        $column_model = new ContentObjectTableColumnModel();
        $cell_renderer = new ContentObjectTableCellRenderer($table_actions);
        parent :: __construct($data_provider, ContentObjectTable :: DEFAULT_NAME, $column_model, $cell_renderer);

        $action = new ObjectTableFormActions();
        $action->set_action(RepoViewer :: PARAM_ACTION);
        if ($parent->get_maximum_select() != RepoViewer :: SELECT_SINGLE)
        {
           $action->add_form_action(new ObjectTableFormAction(RepoViewer :: ACTION_PUBLISHER, Translation :: get('PublishSelected'), false));
        }

        $this->set_form_actions($action);
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(RepoViewer :: PARAM_ID, $ids);
    }
}
?>