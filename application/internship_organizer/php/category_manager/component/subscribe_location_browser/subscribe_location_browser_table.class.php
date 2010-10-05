<?php

require_once dirname(__FILE__) . '/subscribe_location_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribe_location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribe_location_browser_table_cell_renderer.class.php';

class SubscribeLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'subscribe_location_browser_table';

    /**
     * Constructor
     */
    function SubscribeLocationBrowserTable($browser, $parameters, $condition, $category)
    {
        $model = new SubscribeLocationBrowserTableColumnModel();
        $renderer = new SubscribeLocationBrowserTableCellRenderer($browser, $category);
        $data_provider = new SubscribeLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SubscribeLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        $actions = new ObjectTableFormActions(InternshipOrganizerCategoryManager ::PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerCategoryManager :: ACTION_SUBSCRIBE_LOCATION_TO_CATEGORY, Translation :: get('Subscribe')));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
		$ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_REL_LOCATION_ID, $ids);
    }
}
?>