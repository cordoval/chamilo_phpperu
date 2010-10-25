<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/rel_location_browser/rel_location_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/rel_location_browser/rel_location_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/rel_location_browser/rel_location_browser_table_cell_renderer.class.php';

class InternshipOrganizerCategoryRelLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_category_rel_location_browser_table';

   
    function InternshipOrganizerCategoryRelLocationBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerCategoryRelLocationBrowserTableColumnModel();
        $renderer = new InternshipOrganizerCategoryRelLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerCategoryRelLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerCategoryRelLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
     	$actions = new ObjectTableFormActions(InternshipOrganizerCategoryManager ::PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerCategoryManager :: ACTION_UNSUBSCRIBE_LOCATION_FROM_CATEGORY, Translation :: get('Unsubscribe')));
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