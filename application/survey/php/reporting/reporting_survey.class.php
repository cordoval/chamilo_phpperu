<?php 
namespace application\survey;
/**
 * $Id: reporting_survey.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.reporting
 */
/**
 * @author Sven Vanpoucke
 */
//require_once dirname(__FILE__) . '/../survey_data_manager.class.php';
//require_once dirname(__FILE__) . '/../survey_manager/survey_manager.class.php';
//require_once dirname(__FILE__) . '/../trackers/survey_participant_tracker.class.php';

class ReportingSurvey
{
    
    const PARAM_SURVEY_ID_CATEGORY = 'category';
    const PARAM_SURVEY_ID_URL = 'url';
    const PARAM_PARTICIPANT_ID = 'participant';

    function ReportingSurvey()
    {
    
    }

    public static function getSummarySurveyParticipation($params)
    {
        $data = array();
              
        $category = $params[self :: PARAM_SURVEY_ID_CATEGORY];
        $url = $params[self :: PARAM_SURVEY_ID_URL];
        $participant_id = $params[self :: PARAM_PARTICIPANT_ID];
        
        $adm = SurveyDataManager :: get_instance();
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_CATEGORY, $category);
        $publications = $adm->retrieve_survey_publications($condition);
        
        while ($publication = $publications->next_result())
        {
            
        	     	
        	$survey = $publication->get_publication_object();
            $context = $survey->get_context();
            $context_name = $context->get_display_name();
            
            $dummy = new SurveyParticipantTracker();
            $conditions = array();
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $participant_id);
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $publication->get_id());
            
            
            $condition = new AndCondition($conditions);
            $trackers = $dummy->retrieve_tracker_items($condition);
                    
            foreach ($trackers as $tracker)
            {
                             
                $context_id = $tracker->get_context_id();
            	$context_instance = SurveyContext :: get_by_id($context_id, $context->get_type());
                $data[Translation :: get('ContextType')][] = $context_name;
                $data[Translation :: get('Context')][] = $context_instance->get_name();
                $data[Translation :: get('Title')][] = $survey->get_title();
                $data[Translation :: get('Progress')][] = $tracker->get_progress() . '%';
                
                $actions = array();
                
                $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
		        $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('ViewResults'),
		        		Theme :: get_common_image_path() . 'action_view_results.png',
		        		$url . '&' . SurveyManager :: PARAM_PUBLICATION_ID . '=' . $publication->get_id(),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
		        
		        $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('DeleteResults'),
		        		Theme :: get_common_image_path() . 'action_delete.png',
		        		$url . '&delete=aid_' . $publication->get_id(),
		        		ToolbarItem :: DISPLAY_ICON,
		        		true
		        ));
        
                $data[Translation :: get('Action')][] = $toolbar->as_html();
            
            }
        
        }
        
        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($data, $description);
    }

    public static function getSurveyQuestionAnswers($params)
    {
        $pid = $params[SurveyManager :: PARAM_PUBLICATION_ID];
        
        $url = $params['url'];
        $results_export_url = $params['results_export_url'];
        $user_id = $params['user_id'];
        
        $dummy = new SurveyParticipantTracker();
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $user_id);
        $trackers = $dummy->retrieve_tracker_items($condition);
        $participant_id = $trackers[0]->get_id();
        
        $dummy = new SurveyQuestionAnswerTracker();
        $condition = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $participant_id);
        $trackers = $dummy->retrieve_tracker_items($condition);
        
        $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($pid);
        $survey = $pub->get_publication_object();
        $data = array();
        
        foreach ($trackers as $tracker)
        {
            $question_id = $tracker->get_question_cid();
            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_id);

            $data[Translation :: get('Title')][] = $question->get_title();
            $data[Translation :: get('Description')][] = $question->get_description();
            
            $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
		        $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('ViewResults'),
		        		Theme :: get_common_image_path() . 'action_view_results.png',
		        		$url . '&' . SurveyManager :: PARAM_SURVEY_QUESTION_ID . '=' . $question_id,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
		        
                $data[Translation :: get('Action')][] = $toolbar->as_html();
        }
        
        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($data, $description);
    }

    public static function getSurveyQuestionResults($params)
    {
        
        //dump($params);
        $pub_id = $params[SurveyManager :: PARAM_PUBLICATION_ID];
        $question_id = $params[SurveyManager :: PARAM_SURVEY_QUESTION_ID];
        $question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_id);
        $type = $question->get_type();
        //dump($type);
        

        $possible_answers = array();
        
        switch ($question->get_type())
        {
            
            case RatingQuestion :: get_type_name() :
                $possible_answers[] = 'value';
                break;
            case OpenQuestion :: get_type_name() :
            
            case FillInBlanksQuestion :: get_type_name() :
            
            case MultipleChoiceQuestion :: get_type_name() :
                $options = $question->get_options();
                foreach ($options as $option)
                {
                    $possible_answers[] = $option->get_value();
                }
                break;
            case MatchingQuestion :: get_type_name() :
            
            case SelectQuestion :: get_type_name() :
            
            case MatrixQuestion :: get_type_name() :
            
            default :
        
        }
        
        $answer_count = array();
        
        foreach ($possible_answers as $possible_answer)
        {
            $answer_count[] = 0;
        }
        
        //        dump($answer_count);
        

        $dummy = new SurveyParticipantTracker();
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $pub_id);
        $participants = $dummy->retrieve_tracker_items($condition);
        
        $participant_count = 0;
        $answers_count = 0;
        $answers = array();
        foreach ($participants as $participant)
        {
            $participant_count ++;
            $dummy = new SurveyQuestionAnswerTracker();
            $conditions = array();
            $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $question_id);
            $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $participant->get_id());
            $condition = new AndCondition($conditions);
            $trackers = $dummy->retrieve_tracker_items($condition);
            foreach ($trackers as $tracker)
            {
                $answers_count ++;
                $answer = unserialize($tracker->get_answer());
                foreach ($answer as $value)
                {
                    $answer_count[$value] ++;
                    //dump($value);
                }
            }
        }
        
        //$answers_count = count($answers);
        

        //        dump($answer_count);
        //                
        //        dump($participant_count);
        //        dump($answers_count);
        

        $data = array();
        
        //dump($answer_count);
        

        foreach ($answer_count as $key => $count)
        {
            //        	dump($key);
            //        	dump($value);
            $answer = $possible_answers[$key];
            $value = ($count / $participant_count) * 100;
            $data[$answer][$question->get_title()] = ($count / $participant_count) * 100;
        }
        
        if ($answers_count < $participant_count)
        {
            $data[Translation :: get_instance('noanswer')][] = (($participant_count - $answers_count) / $participant_count) * 100;
        }
        
        //dump($data);
        

        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_VERTICAL;
        return Reporting :: getSerieArray($data, $description);
    }

}
?>