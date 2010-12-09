<?php
namespace application\survey;

//require_once dirname(__FILE__) . '/../survey_reporting_block.class.php';
//require_once dirname(__FILE__) . '/../../survey_manager/survey_manager.class.php';
//require_once (dirname(__FILE__) . '/../../trackers/survey_question_answer_tracker.class.php');
//require_once (dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php');
//require_once Path :: get_repository_path() . 'lib/content_object/survey/analyzer/analyzer.class.php';

class SurveyTotalUserResultViewReportingBlock extends SurveyReportingBlock
{
    
    const NOT_VIEWED = 0;
    const VIEWED = 1;
    
    private $publication_id;
    private $viewed;
    private $parent;

    function SurveyTotalUserResultViewReportingBlock($parent, $publication_id, $viewed)
    {
        parent :: __construct($parent);
        $this->parent = $parent;
        $this->publication_id = $publication_id;
        $this->viewed = $viewed;
    }

    public function get_title()
    {
        if ($this->viewed == 1)
        {
            $viewed = Translation :: get('viewed');
        }
        else
        {
            $viewed = Translation :: get('notviewed');
        }
        $title = Translation :: get('TotalUserReportingResultView');
        $title = $title . ' ' . $viewed;
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
        
        $user_ids = array();
        
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
            
            $trackers = Tracker :: get_data(VisitTracker :: get_table_name(), UserManager :: APPLICATION_NAME, $condition);
            
            while ($tracker = $trackers->next_result())
            {
                $user_ids[] = $tracker->get_user_id();
            }
        }
        
        $viewed_users = array_unique($user_ids);
               
        $reporting_users = SurveyRights :: get_allowed_users(SurveyRights :: RIGHT_REPORTING, $this->publication_id, SurveyRights :: TYPE_PUBLICATION);
        $reporting_users = array_unique($reporting_users);
        
        $not_viewed_users = array_diff($reporting_users, $viewed_users);
              
      	 $categories = array();
        $nr = 0;
        
        if($this->viewed == 1){
        	$user_count = count($viewed_users);
        	$user_ids = $viewed_users;
        }else{
        	$user_count = count(array_unique($not_viewed_users));
        	$user_ids = $not_viewed_users;
        }
        
        
        while ($user_count > 0)
        {
            $nr ++;
            $categories[] = $nr;
            $user_count --;
        }
        
        $firstname = Translation :: get('Firstname');
        $lastname = Translation :: get('Lastname');
        $email = Translation :: get('Email');
        $rows = array($firstname, $lastname, $email);
        
        $reporting_data = new ReportingData();
        $reporting_data->set_categories($categories);
        $reporting_data->set_rows($rows);
        
        $nr = 0;
        foreach ($user_ids as $user_id)
        {
            $nr ++;
            $user = UserDataManager :: get_instance()->retrieve_user($user_id);
            $reporting_data->add_data_category_row($nr, $firstname, $user->get_firstname());
            $reporting_data->add_data_category_row($nr, $lastname, $user->get_lastname());
            $reporting_data->add_data_category_row($nr, $email, $user->get_email());
        
        }
        return $reporting_data;
    }
}

?>