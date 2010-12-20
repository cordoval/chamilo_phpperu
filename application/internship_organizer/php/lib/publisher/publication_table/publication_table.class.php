<?php
namespace application\internship_organizer;

use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;

/**
 * $Id: publication_browser_table.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.publication_browser
 */
require_once dirname(__FILE__) . '/publication_table_data_provider.class.php';
require_once dirname(__FILE__) . '/publication_table_column_model.class.php';
require_once dirname(__FILE__) . '/publication_table_cell_renderer.class.php';

/**
 * Table to display a list of publications
 *
 * @author Sven Vanpoucke
 * @author
 */
class InternshipOrganizerPublicationTable extends ObjectTable
{
    const DEFAULT_NAME = 'internship_organizer_publication_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerPublicationTableColumnModel();
        $renderer = new InternshipOrganizerPublicationTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPublicationTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);

        $actions = new ObjectTableFormActions(__NAMESPACE__, InternshipOrganizerPeriodManager :: PARAM_ACTION);

        //         $actions->add_form_action(new ObjectTableFormAction(InternshipOrganizerAgreementManager :: ACTION_DELETE_PUBLICATION, Translation :: get('Delete')));


        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);

     //        $ids = self :: get_selected_ids($class);
    //        Request :: set_get(InternshipOrganizerAgreementManager :: PARAM_PUBLICATION_ID, $ids);
    }
}
?>