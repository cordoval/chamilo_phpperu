<?php
/**
 * $Id: document_downloader.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component
 */
require_once dirname(__FILE__) . '/../../trackers/survey_question_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/../../trackers/survey_survey_attempts_tracker.class.php';

class SurveyManagerDocumentDownloaderComponent extends SurveyManager
{

    function run()
    {
        if (Request :: get(SurveyManager :: PARAM_PUBLICATION_ID))
        {
            $id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
            $type = SurveyManager :: PARAM_PUBLICATION_ID;
            $filenames = $this->save_survey_docs($id);
        }
        else 
            if (Request :: get('tid'))
            {
                $id = Request :: get('tid');
                $type = 'tid';
                $track = new SurveySurveyAttemptsTracker();
                $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_ID, $id);
                $user_surveys = $track->retrieve_tracker_items($condition);
                $filenames = $this->save_user_survey_docs($user_surveys[0]);
            }
        if (count($filenames) > 0)
            $this->send_files($filenames, $id);
        else
        {
            $this->redirect_to_previous($type, $id);
        }
    }

    function save_survey_docs($survey_id)
    {
        //$publication = SurveyDataManager :: get_instance()->retrieve_content_object_publication($survey_id);
        $track = new SurveySurveyAttemptsTracker();
        $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_SURVEY_ID, $survey_id);
        $user_surveys = $track->retrieve_tracker_items($condition);
        //dump($user_surveys);
        foreach ($user_surveys as $user_survey)
        {
            $ua_filenames = $this->save_user_survey_docs($user_survey);
            foreach ($ua_filenames as $file)
            {
                $filenames[] = $file;
            }
        }
        return $filenames;
    }

    function save_user_survey_docs($user_survey)
    {
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($user_survey->get_survey_id());
        $survey = $publication->get_publication_object();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey->get_id(), ComplexContentObjectItem :: get_table_name());
        $clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition);
        
        while ($clo_question = $clo_questions->next_result())
        {
            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($clo_question->get_ref());
            if ($question->get_type() == 'open_question')
            {
                if ($question->get_question_type() == OpenQuestion :: TYPE_DOCUMENT || $question->get_question_type() == OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT)
                {
                    $c_questions[] = $clo_question;
                    $questions[] = $question;
                }
            }
        }
        
        foreach ($questions as $i => $question)
        {
            $clo_question = $c_questions[$i];
            $track = new SurveyQuestionAttemptsTracker();
            $conditiona = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_SURVEY_ATTEMPT_ID, $user_survey->get_id());
            $conditionq = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $clo_question->get_id());
            $condition = new AndCondition(array($conditiona, $conditionq));
            $user_questions = $track->retrieve_tracker_items($condition);
            //print_r($condition);
            //dump($user_questions); 
            if ($question->get_question_type() == OpenQuestion :: TYPE_DOCUMENT)
            {
                $user_question = $user_questions[0];
            }
            else
            {
                $user_question = $user_questions[1];
            }
            
            if ($user_question != null)
            {
                $answer = unserialize($user_question->get_answer());
                $document = RepositoryDataManager :: get_instance()->retrieve_content_object($answer[2], Document :: get_type_name());
                $filenames[] = Path :: get(SYS_REPO_PATH) . $document->get_path();
            }
        }
        
        return $filenames;
    }

    function redirect_to_previous($type, $id)
    {
        $params = array(Tool :: PARAM_ACTION => SurveyTool :: ACTION_VIEW_RESULTS, $type => $id);
        $this->redirect(Translation :: get('NoDocumentsForSurvey'), false, $params);
    }

    function send_files($filenames, $survey_id)
    {
        $temp_dir = Path :: get(SYS_TEMP_PATH) . 'retrieve_docs/' . $survey_id . '/';
        if (! is_dir($temp_dir))
        {
            mkdir($temp_dir, '0777', true);
        }
        
        foreach ($filenames as $filename)
        {
            $newfile = $temp_dir . basename($filename);
            Filesystem :: copy_file($filename, $newfile);
        }
        
        $zip = Filecompression :: factory();
        $zip->set_filename('survey_documents', 'zip');
        $path = $zip->create_archive($temp_dir);
        
        Filesystem :: remove($temp_dir);
        
        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Cache-Control: public');
        header('Pragma: no-cache');
        header('Content-type: application/octet-stream');
        header('Content-length: ' . filesize($path));
        
        if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
        {
            header('Content-Disposition: filename= ' . basename($path));
        }
        else
        {
            header('Content-Disposition: attachment; filename= ' . basename($path));
        }
        
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
        {
            header('Pragma: ');
            header('Cache-Control: ');
            header('Cache-Control: public'); // IE cannot download from sessions without a cache
        }
        
        header('Content-Description: ' . basename($path));
        header('Content-transfer-encoding: binary');
        $fp = fopen($path, 'r');
        fpassthru($fp);
        fclose($fp);
        Filesystem :: remove($path);
    }
}
?>