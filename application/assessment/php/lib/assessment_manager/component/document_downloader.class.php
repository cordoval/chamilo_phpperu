<?php
namespace application\assessment;

use application\weblcms\tool\assessment\AssessmentTool;
use application\weblcms\Tool;

use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;
use repository\content_object\assessment_open_question\AssessmentOpenQuestion;
use repository\content_object\document\Document;

use common\libraries\AndCondition;
use common\libraries\Filecompression;
use common\libraries\Filesystem;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Path;
/**
 * $Id: document_downloader.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */

require_once dirname(__FILE__) . '/../../../trackers/assessment_question_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/assessment_assessment_attempts_tracker.class.php';

class AssessmentManagerDocumentDownloaderComponent extends AssessmentManager
{

    function run()
    {
        if (Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION))
        {
            $id = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
            $type = AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION;
            $filenames = $this->save_assessment_docs($id);
        }
        else
            if (Request :: get('tid'))
            {
                $id = Request :: get('tid');
                $type = 'tid';
                $track = new AssessmentAssessmentAttemptsTracker();
                $condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ID, $id);
                $user_assessments = $track->retrieve_tracker_items($condition);
                $filenames = $this->save_user_assessment_docs($user_assessments[0]);
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
    	$breadcrumbtrail->add_help('assessment_builder');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('AssessmentManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
    	return array(self :: PARAM_ASSESSMENT_PUBLICATION, 'tid');
    }

    function save_assessment_docs($assessment_id)
    {
        //$publication = AssessmentDataManager :: get_instance()->retrieve_content_object_publication($assessment_id);
        $track = new AssessmentAssessmentAttemptsTracker();
        $condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $assessment_id);
        $user_assessments = $track->retrieve_tracker_items($condition);
        //dump($user_assessments);
        foreach ($user_assessments as $user_assessment)
        {
            $ua_filenames = $this->save_user_assessment_docs($user_assessment);
            foreach ($ua_filenames as $file)
            {
                $filenames[] = $file;
            }
        }
        return $filenames;
    }

    function save_user_assessment_docs($user_assessment)
    {
        $publication = AssessmentDataManager :: get_instance()->retrieve_assessment_publication($user_assessment->get_assessment_id());
        $assessment = $publication->get_publication_object();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment->get_id(), ComplexContentObjectItem :: get_table_name());
        $clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition);

        while ($clo_question = $clo_questions->next_result())
        {
            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($clo_question->get_ref());
            if ($question->get_type() == 'open_question')
            {
                if ($question->get_question_type() == AssessmentOpenQuestion :: TYPE_DOCUMENT || $question->get_question_type() == AssessmentOpenQuestion :: TYPE_OPEN_WITH_DOCUMENT)
                {
                    $c_questions[] = $clo_question;
                    $questions[] = $question;
                }
            }
        }

        foreach ($questions as $i => $question)
        {
            $clo_question = $c_questions[$i];
            $track = new AssessmentQuestionAttemptsTracker();
            $conditiona = new EqualityCondition(AssessmentQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $user_assessment->get_id());
            $conditionq = new EqualityCondition(AssessmentQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $clo_question->get_id());
            $condition = new AndCondition(array($conditiona, $conditionq));
            $user_questions = $track->retrieve_tracker_items($condition);
            //print_r($condition);
            //dump($user_questions);
            if ($question->get_question_type() == AssessmentOpenQuestion :: TYPE_DOCUMENT)
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
        $params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, $type => $id);
        $this->redirect(Translation :: get('NoDocumentsForAssessment'), false, $params);
    }

    function send_files($filenames, $assessment_id)
    {
        $temp_dir = Path :: get(SYS_TEMP_PATH) . 'retrieve_docs/' . $assessment_id . '/';
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
        $zip->set_filename('assessment_documents', 'zip');
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