<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
use common\libraries\Translation;
use common\libraries\ObjectTableFormAction;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/subscribe_location_browser/subscribe_location_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/subscribe_location_browser/subscribe_location_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/subscribe_location_browser/subscribe_location_browser_table_cell_renderer.class.php';

class InternshipOrganizerSubscribeLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'subcribe_location_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerSubscribeLocationBrowserTableColumnModel();
        $renderer = new InternshipOrganizerSubscribeLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerSubscribeLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerSubscribeLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerAgreementManager :: PARAM_SUBSCRIBE_SELECTED, Translation :: get('Subscribe'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>