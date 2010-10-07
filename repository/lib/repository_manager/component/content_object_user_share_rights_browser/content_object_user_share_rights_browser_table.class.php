<?php

require_once dirname(__FILE__) . '/content_object_user_share_rights_browser_table_data_provider.class.php';

require_once dirname(__FILE__) . '/content_object_user_share_rights_browser_table_cell_renderer.class.php';

require_once dirname(__FILE__) . '/content_object_user_share_rights_browser_table_column_model.class.php';

/**
 * Table to display the content object share rights.
 * @author Pieterjan Broekaert
 */
class ContentObjectUserShareRightsBrowserTable extends ObjectTable
{

    function ContentObjectUserShareRightsBrowserTable($browser, $parameters, $condition)
    {
        $model = new ContentObjectUserShareRightsBrowserTableColumnModel();
        $renderer = new ContentObjectUserShareRightsBrowserTableCellRenderer($browser);
        $data_provider = new ContentObjectUserShareRightsBrowserTableDataProvider($browser, $condition);
        ObjectTable :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);

        $table_form_actions = new ObjectTableFormActions();
        $table_form_actions->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_CONTENT_OBJECT_SHARE_DELETER, Translation :: get('delete'), true));

        $this->set_form_actions($table_form_actions);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));

        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID));
        Request :: set_get(ContentObjectUserShare :: PARAM_TYPE, ContentObjectUserShare :: TYPE_USER_SHARE);
        Request :: set_get(RepositoryManager :: PARAM_TARGET_USER, $ids);

    }

}

?>