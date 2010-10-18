<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/browser/browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/browser/browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category_manager/component/browser/browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class InternshipOrganizerCategoryBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_category_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function InternshipOrganizerCategoryBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerCategoryBrowserTableColumnModel();
        $renderer = new InternshipOrganizerCategoryBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerCategoryBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerCategoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        $actions = new ObjectTableFormActions(InternshipOrganizerCategoryManager :: PARAM_ACTION);
        
        $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerCategoryManager :: ACTION_DELETE_CATEGORY, Translation :: get('RemoveSelected')));
        $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerCategoryManager :: ACTION_TRUNCATE_CATEGORY, Translation :: get('TruncateSelected')));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID, $ids);
    }
}
?>