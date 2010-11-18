<?php 
namespace application\survey;

use common\libraries\StaticTableColumn;


//require_once dirname(__FILE__) . '/../../../tables/participant_table/default_participant_table_column_model.class.php';
//require_once dirname(__FILE__) . '/../../../trackers/survey_participant_tracker.class.php';

class SurveyParticipantBrowserTableColumnModel extends DefaultParticipantTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SurveyParticipantBrowserTableColumnModel()
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