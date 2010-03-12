<?php
/**
 * $Id: survey_publication_browser_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.survey_publication_browser
 */

require_once dirname(__FILE__) . '/../../../tables/test_survey_participant_table/default_test_survey_participant_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../trackers/survey_participant_tracker.class.php';

/**
 * Table column model for the survey_publication browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class TestSurveyParticipantBrowserTableColumnModel extends DefaultTestSurveyParticipantTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function TestSurveyParticipantBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>