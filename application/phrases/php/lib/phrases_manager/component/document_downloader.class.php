<?php
namespace application\phrases;

use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use repository\RepositoryDataManager;
use repository\OpenQuestion;
use common\libraries\Path;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerDocumentDownloaderComponent extends PhrasesManager
{

    function run()
    {
        if (Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION))
        {
            $id = Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION);
            $type = PhrasesManager :: PARAM_PHRASES_PUBLICATION;
            $filenames = $this->save_phrases_docs($id);
        }
        else
            if (Request :: get('tid'))
            {
                $id = Request :: get('tid');
                $type = 'tid';
                $track = new PhrasesAdaptiveAssessmentAttemptTracker();
                $condition = new EqualityCondition(PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_ID, $id);
                $user_phrasess = $track->retrieve_tracker_items($condition);
                $filenames = $this->save_user_phrases_docs($user_phrasess[0]);
            }
        if (count($filenames) > 0)
            $this->send_files($filenames, $id);
        else
        {
            $this->redirect_to_previous($type, $id);
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_builder');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION, 'tid');
    }

    function save_phrases_docs($phrases_id)
    {
        //$publication = PhrasesDataManager :: get_instance()->retrieve_content_object_publication($phrases_id);
        $track = new PhrasesAdaptiveAssessmentAttemptTracker();
        $condition = new EqualityCondition(PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ID, $phrases_id);
        $user_phrasess = $track->retrieve_tracker_items($condition);
        //dump($user_phrasess);
        foreach ($user_phrasess as $user_phrases)
        {
            $ua_filenames = $this->save_user_phrases_docs($user_phrases);
            foreach ($ua_filenames as $file)
            {
                $filenames[] = $file;
            }
        }
        return $filenames;
    }

    function save_user_phrases_docs($user_phrases)
    {
        $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($user_phrases->get_phrases_id());
        $phrases = $publication->get_publication_object();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $phrases->get_id(), ComplexContentObjectItem :: get_table_name());
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
            $track = new PhrasesAdaptiveAssessmentQuestionAttemptsTracker();
            $conditiona = new EqualityCondition(PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID, $user_phrases->get_id());
            $conditionq = new EqualityCondition(PhrasesAdaptiveAssessmentQuestionAttemptsTracker :: PROPERTY_COMPLEX_QUESTION_ID, $clo_question->get_id());
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
        $params = array(Tool :: PARAM_ACTION => PhrasesTool :: ACTION_VIEW_RESULTS,
                $type => $id);
        $this->redirect(Translation :: get('NoDocumentsForPhrases'), false, $params);
    }

    function send_files($filenames, $phrases_id)
    {
        $temp_dir = Path :: get(SYS_TEMP_PATH) . 'retrieve_docs/' . $phrases_id . '/';
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
        $zip->set_filename('phrases_documents', 'zip');
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