<?php
namespace application\survey;

use common\libraries\Translation;
use reporting\ReportingData;
use common\libraries\EqualityCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\AndCondition;
use user\VisitTracker;
use tracking\Tracker;
use user\UserManager;

class SurveyTotalResultViewReportingBlock extends SurveyReportingBlock
{
    
    const PERCENTAGE = 'percentage';
    const ABSOLUTE = 'absolute';
    
    private $publication_id;
    private $analyse_type;
    private $parent;

    function __construct($parent, $publication_id, $analyse_type)
    {
        parent :: __construct($parent);
        $this->parent = $parent;
        $this->publication_id = $publication_id;
        $this->analyse_type = $analyse_type;
    }

    public function get_title()
    {
        $title = Translation :: get('TotalReportingResultView');
        $title = $title . ' ' . $this->analyse_type;
        return $title;
    }

    public function count_data()
    {
        return $this->create_reporting_data();
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }

    private function create_reporting_data()
    {
        $reporting_data = new ReportingData();
        
        $condition = new EqualityCondition(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_PUBLICATION_ID, $this->publication_id);
        $publicationrelreportingtemplates = SurveyDataManager :: get_instance()->retrieve_survey_publication_rel_reporting_template_registrations($condition);
        
        while ($publicationrelreportingtemplate = $publicationrelreportingtemplates->next_result())
        {
            
            $publication_rel_template_id = $publicationrelreportingtemplate->get_id();
            if ($this->parent->get_id() == $publicationrelreportingtemplate->get_reporting_template_registration_id())
            {
                continue;
            }
            
            $context_template_id = $publicationrelreportingtemplate->get_level();
            
            $conditions = array();
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*application=survey*');
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*action=reporting*');
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*go=reporting*');
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*publication_id=' . $this->publication_id . '*');
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*publication_rel_reporting_template_id=' . $publication_rel_template_id . '*');
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*context_template_id=' . $context_template_id . '*');
            $condition = new AndCondition($conditions);
            
            $trackers = Tracker :: get_data(VisitTracker :: CLASS_NAME, UserManager :: APPLICATION_NAME, $condition);
            
            $user_ids = array();
            
            while ($tracker = $trackers->next_result())
            {
                $user_ids[] = $tracker->get_user_id();
            }
            
            $viewed_users = array_unique($user_ids);
            
            $viewed_users_count = count($viewed_users);
            
            $reporting_users = SurveyRights :: get_allowed_users(SurveyRights :: RIGHT_REPORTING, $this->publication_id, SurveyRights :: TYPE_PUBLICATION);
            $reporting_users = array_unique($reporting_users);
            
            $not_viewed_users = array_diff($reporting_users, $viewed_users);
            
            $not_viewed_users_count = count(array_unique($not_viewed_users));
            
            $categorie = $publicationrelreportingtemplate->get_name();
            
            $reporting_data->add_category($categorie);
            
            $viewed_row = Translation :: get('viewed');
            $not_viewed_row = Translation :: get('notviewed');
            
            $rows = array($viewed_row, $not_viewed_row);
            
            $reporting_data->set_rows($rows);
            
            if ($this->analyse_type == self :: ABSOLUTE)
            {
                $reporting_data->add_data_category_row($categorie, $viewed_row, $viewed_users_count);
                $reporting_data->add_data_category_row($categorie, $not_viewed_row, $not_viewed_users_count);
            
            }
            else
            {
                $total = $not_viewed_users_count + $viewed_users_count;
                $viewed = number_format($viewed_users_count / $total * 100,2);
                $not_viewed = number_format($not_viewed_users_count / $total * 100,2);
                $reporting_data->add_data_category_row($categorie, $viewed_row, $viewed);
                $reporting_data->add_data_category_row($categorie, $not_viewed_row, $not_viewed);
            
            }
        
        }
        
        return $reporting_data;
    }
}

?>