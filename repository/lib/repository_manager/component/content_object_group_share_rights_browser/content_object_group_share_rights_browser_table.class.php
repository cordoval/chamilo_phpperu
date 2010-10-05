<?php

require_once dirname(__FILE__) . '/content_object_group_share_rights_browser_table_data_provider.class.php';

require_once dirname(__FILE__) . '/content_object_group_share_rights_browser_table_cell_renderer.class.php';

require_once dirname(__FILE__) . '/content_object_group_share_rights_browser_table_column_model.class.php';

/**
 * Table to display the content object share rights.
 * @author Pieterjan Broekaert
 */
class ContentObjectGroupShareRightsBrowserTable extends ObjectTable
{

    function ContentObjectGroupShareRightsBrowserTable($browser, $parameters, $condition)
    {
        $model = new ContentObjectGroupShareRightsBrowserTableColumnModel();
        $renderer = new ContentObjectGroupShareRightsBrowserTableCellRenderer($browser);
        $data_provider = new ContentObjectGroupShareRightsBrowserTableDataProvider($browser, $condition);
        ObjectTable :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);

        $table_form_actions = new ObjectTableFormActions();
        $table_form_actions->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_CONTENT_OBJECT_SHARE_DELETER, Translation :: get('delete'), true));

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));//the selected groups

        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID));
        Request :: set_get(ContentObjectGroupShare :: PARAM_TYPE, ContentObjectGroupShare :: TYPE_GROUP_SHARE);
        Request :: set_get(RepositoryManager :: PARAM_TARGET_GROUP, $ids);
    }

}

?>