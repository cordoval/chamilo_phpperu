<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;
/**
 * $Id: repository_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/settings_manager_table_data_provider.class.php';
require_once dirname(__FILE__) . '/settings_manager_table_column_model.class.php';
require_once dirname(__FILE__) . '/settings_manager_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class SettingsManagerTable extends ObjectTable
{
    const DEFAULT_NAME = 'settings_manager_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function SettingsManagerTable($browser, $parameters)
    {
        $model = new SettingsManagerTableColumnModel();
        $renderer = new SettingsManagerTableCellRenderer($browser);
        $data_provider = new SettingsManagerTableDataProvider($browser);
        parent :: __construct($data_provider, SettingsManagerTable :: DEFAULT_NAME, $model, $renderer);
        if ($browser instanceof MediamosaExternalRepositoryManagerSettingsManagerComponent)
        {
            $actions = array();
            /*$actions[RepositoryManager :: PARAM_RECYCLE_SELECTED] = Translation :: get('RemoveSelected');
			$actions[RepositoryManager :: PARAM_MOVE_SELECTED] = Translation :: get('MoveSelected');
			$actions[RepositoryManager :: PARAM_PUBLISH_SELECTED] = Translation :: get('PublishSelected');*/

            $actions[] = new ObjectTableFormAction(MediamosaExternalRepositoryManager :: ACTION_ADD_SETTING, Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES));
        }

        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
    }

/*static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
    }*/
}
?>