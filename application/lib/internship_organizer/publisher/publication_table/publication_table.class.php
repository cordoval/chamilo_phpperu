<?php
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
    const DEFAULT_NAME = 'publication_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerPublicationTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerPublicationTableColumnModel();
        $renderer = new InternshipOrganizerPublicationTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPublicationTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        if ($browser->get_user()->is_platform_admin())
        {
//            $actions[] = new ObjectTableFormAction(InternshipOrganizerManager :: PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS, Translation :: get('RemoveSelected'));
//            $actions[] = new ObjectTableFormAction(InternshipOrganizerManager :: PARAM_MAIL_PARTICIPANTS, Translation :: get('InviteParticipants'));
        }
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>