<?php
namespace application\survey;

use reporting\ReportingChartFormatter;
use reporting\ReportingFormatter;
use reporting\ReportingData;

use common\libraries\Translation;
use common\libraries\EqualityCondition;

class SurveyParticipantMailReportingBlock extends SurveyReportingBlock
{

    public function count_data()
    {

        require_once (dirname(__FILE__) . '/../../trackers/survey_participant_mail_tracker.class.php');

        $conditions = array();

        $filter_parameters = $this->get_filter_parameters();

        $publication_id = $filter_parameters[SurveyReportingFilterWizard :: PARAM_PUBLICATION_ID];
        $condition = new EqualityCondition(SurveyParticipantMailTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $publication_id);

        $tracker = new SurveyParticipantMailTracker();
        $trackers = $tracker->retrieve_tracker_items_result_set($condition);

        $mails[Translation :: get(SurveyParticipantMailTracker :: STATUS_MAIL_NOT_SEND)] = 0;
        $mails[Translation :: get(SurveyParticipantMailTracker :: STATUS_MAIL_SEND)] = 0;

        while ($tracker = $trackers->next_result())
        {
            $status = $tracker->get_status();
            switch ($status)
            {
                case SurveyParticipantMailTracker :: STATUS_MAIL_NOT_SEND :
                    $mails[Translation :: get(SurveyParticipantMailTracker :: STATUS_MAIL_NOT_SEND)] ++;
                    break;
                case SurveyParticipantMailTracker :: STATUS_MAIL_SEND :
                    $mails[Translation :: get(SurveyParticipantMailTracker :: STATUS_MAIL_SEND)] ++;
                    break;

            }
        }
        $reporting_data = new ReportingData();

        $reporting_data->set_categories(array(Translation :: get('MailNotSend'), Translation :: get('MailSend')));
        $reporting_data->set_rows(array(Translation :: get('Count')));

        $reporting_data->add_data_category_row(Translation :: get('MailNotSend'), Translation :: get('Count'), $mails[Translation :: get(SurveyParticipantMailTracker :: STATUS_MAIL_NOT_SEND)]);
        $reporting_data->add_data_category_row(Translation :: get('MailSend'), Translation :: get('Count'), $mails[Translation :: get(SurveyParticipantMailTracker :: STATUS_MAIL_SEND)]);

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter :: DISPLAY_PIE] = Translation :: get('Chart:Pie');
        $modes[ReportingChartFormatter :: DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter :: DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter :: DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }
}
?>