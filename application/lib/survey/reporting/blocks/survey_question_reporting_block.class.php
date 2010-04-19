<?php
require_once dirname(__FILE__) . '/../survey_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../survey_manager/survey_manager.class.php';

class SurveyQuestionReportingBlock extends SurveyReportingBlock
{
    
    const NO_ANSWER = 'noAnswer';
    
    private $question;
    private $option_matches;
    private $answer_count;

    public function count_data()
    {
        require_once (dirname(__FILE__) . '/../../trackers/survey_question_answer_tracker.class.php');
        require_once (dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php');
        
        $conditions = array();
        
        $publication_id = $this->get_survey_publication_id();
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $publication_id);
        
        $tracker = new SurveyParticipantTracker();
        $trackers = $tracker->retrieve_tracker_items_result_set($condition);
        
        $users_ids = array();
        $users[Translation :: get(SurveyParticipantTracker :: STATUS_FINISHED)] = 0;
        $users[Translation :: get(SurveyParticipantTracker :: STATUS_NOTSTARTED)] = 0;
        $users[Translation :: get(SurveyParticipantTracker :: STATUS_STARTED)] = 0;
        
        while ($tracker = $trackers->next_result())
        {
            $users_ids[] = $tracker->get_id();
            $status = $tracker->get_status();
            switch ($status)
            {
                case SurveyParticipantTracker :: STATUS_FINISHED :
                    $users[Translation :: get(SurveyParticipantTracker :: STATUS_FINISHED)] ++;
                    break;
                case SurveyParticipantTracker :: STATUS_NOTSTARTED :
                    $users[Translation :: get(SurveyParticipantTracker :: STATUS_NOTSTARTED)] ++;
                    break;
                case SurveyParticipantTracker :: STATUS_STARTED :
                    $users[Translation :: get(SurveyParticipantTracker :: STATUS_STARTED)] ++;
                    break;
            
            }
        }
        
        $question_id = $this->get_survey_question_id();
        $this->question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_id);
        $condition = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $question_id);
        
        $tracker = new SurveyQuestionAnswerTracker();
        $trackers = $tracker->retrieve_tracker_items_result_set($condition);
        
        $reporting_data = new ReportingData();
        $categories = $this->get_question_options();
        $rows = $this->get_question_matches();
//        $rows[] = self :: NO_ANSWER;
        //		
        //		dump($rows);
        //		
        $category_count = count($categories);
        
        while ($category_count >= 0)
        {
            $row_count = count($rows) - 1;
            while ($row_count >= 0)
            {
                $this->answer_count[$category_count][$row_count] = 0;
                $row_count --;
            }
            $this->answer_count[$category_count][self :: NO_ANSWER] = 0;
            $category_count --;
        }
        //		
        //		dump($this->answer_count);
        //		
        while ($tracker = $trackers->next_result())
        {
            $this->add_answer_count($tracker->get_answer());
        }
        
                	foreach ($rows as $row)
            {
        $reporting_data->add_row(strip_tags($row));
            }
//        $rows[] = self :: NO_ANSWER;
        
//        dump($this->answer_count);
        
        foreach ($categories as $category_key =>$category)
        {
            //            $reporting_data->set_categories($category);
            //            $reporting_data->set_rows($rows);
//            dump($category);
        	$reporting_data->add_category($category);
          
        	foreach ($rows as $row_key =>$row)
            {
//                dump($row);
            	$reporting_data->add_data_category_row($category, strip_tags($row),$this->answer_count[$category_key][$row_key] );
//                $reporting_data->add_data_category_row($row, Translation :: get('LastAccess'), $date);
//                $reporting_data->add_data_category_row($row, Translation :: get('Clicks'), count($trackerdata));
//                $reporting_data->add_data_category_row($row, Translation :: get('Publications'), $link_pub);
            }
        	$reporting_data->add_data_category_row($category, self :: NO_ANSWER ,$this->answer_count[$category_key][self :: NO_ANSWER] );
            
        }
        
        
        //		$reporting_data->set_categories ( array (Translation::get (  SurveyParticipantTracker::STATUS_FINISHED ), Translation::get ( SurveyParticipantTracker::STATUS_NOTSTARTED ) , Translation::get ( SurveyParticipantTracker::STATUS_STARTED )) );
        //		$reporting_data->set_rows ( array (Translation::get ( 'Count' ) ) );
        //		foreach ($rows as $row) {
        //			foreach ($categories as $category) {
        //				$reporting_data->add_data_category_row ( $row, $category, $users [Translation::get ( SurveyParticipantTracker::STATUS_FINISHED )] );
        //				
        //			};
        //		}
        

        //		$reporting_data->add_data_category_row ( Translation::get ( SurveyParticipantTracker::STATUS_FINISHED ), Translation::get ( 'Count' ), $users [Translation::get ( SurveyParticipantTracker::STATUS_FINISHED )] );
        //		$reporting_data->add_data_category_row ( Translation::get ( SurveyParticipantTracker::STATUS_NOTSTARTED ), Translation::get ( 'Count' ), $users [Translation::get ( SurveyParticipantTracker::STATUS_NOTSTARTED )] );
        //		$reporting_data->add_data_category_row ( Translation::get ( SurveyParticipantTracker::STATUS_STARTED ), Translation::get ( 'Count' ), $users [Translation::get ( SurveyParticipantTracker::STATUS_STARTED )] );
        //		
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

    private function get_question_options()
    {
        
        $options = array();
        $type = $this->question->get_type();
        switch ($type)
        {
            case ContentObject :: class_to_type(SurveyMatrixQuestion :: CLASS_NAME) :
                $opts = $this->question->get_options();
                foreach ($opts as $option)
                {
                    $options[] = $option->get_value();
                }
                break;
            
            default :
                ;
                break;
        }
        return $options;
    }

    private function get_question_matches()
    {
        $matches = array();
        $type = $this->question->get_type();
        switch ($type)
        {
            case ContentObject :: class_to_type(SurveyMatrixQuestion :: CLASS_NAME) :
                $matchs = $this->question->get_matches();
                foreach ($matchs as $match)
                {
                    $matches[] = $match;
                }
                break;
            
            default :
                ;
                break;
        }
        //		$matches [] = TransLation::get ( self::NO_ANSWER );
        return $matches;
    }

    private function add_answer_count($answers)
    {
        
        //options are the keys of the answer array
        //the matches are the values of the $option arrays
        

        //check the keys against question options indexes that have a answer all others have no answer
        // register the chosen match with a +1 count;
        

        //	dump(array_keys($answers));
        $options_answered = array();
        foreach ($answers as $key => $option)
        {
            //			dump('option');
            //			dump($key);
            $options_answered[] = $key;
            foreach ($option as $match)
            {
                $this->answer_count[$key][$match] ++;
                //				dump('match');
            //				dump($match);
            }
        }
        $options = array();
        foreach ($this->answer_count as $key => $option)
        {
            $options[] = $key;
        }
        //		dump($options_answered);
        //		dump($options);
        $options_not_answered = array_diff($options, $options_answered);
        //		dump($options_not_answered);
        foreach ($options_not_answered as $option)
        {
            $this->answer_count[$option][self :: NO_ANSWER] ++;
           
        }
        
       dump($this->answer_count);
        
    //for each option without answer add the no answer +1 count;
    

    }

}
?>