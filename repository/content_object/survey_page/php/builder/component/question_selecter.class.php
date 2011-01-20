<?php
namespace repository\content_object\survey_page;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;

use common\extensions\repo_viewer\RepoViewer;

use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;
use repository\ComplexBuilder;

/**
 * $Id: question_selecter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component
 */

class SurveyPageBuilderQuestionSelecterComponent extends SurveyPageBuilder
{

    function run()
    {
        $assessment_id = Request :: get(self :: PARAM_SURVEY_PAGE_ID);
        if ($assessment_id)
        {
            $clois = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment_id, ComplexContentObjectItem :: get_table_name()));
            while ($cloi = $clois->next_result())
            {
                $question_ids[] = $cloi->get_ref();
            }
        }
        else
        {
            $question_ids = Request :: get(self :: PARAM_QUESTION_ID);
            if (! is_array($question_ids))
                $question_ids = array($question_ids);
        }

        if (count($question_ids) == 0)
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(
                    ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE)), $this->get_root_content_object()->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(
                    ComplexBuilder :: PARAM_BUILDER_ACTION => self :: ACTION_MERGE_SURVEY_PAGE,
                    RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER,
                    RepoViewer :: PARAM_ID => Request :: get(RepoViewer :: PARAM_ID))), Translation :: get('MergeSurveyPage')));
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NoQuestionsSelected'));
            $this->display_footer();
            exit();
        }

        $succes = true;

        $parent = $this->get_root_content_object()->get_id();

        foreach ($question_ids as $question_id)
        {
            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_id);
            $cloi = ComplexContentObjectItem :: factory($question->get_type());
            $cloi->set_parent($parent);
            $cloi->set_ref($question_id);
            $cloi->set_user_id($this->get_user_id());
            $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($parent));
            $succes &= $cloi->create();
        }

        $message = $succes ? Translation :: get('QuestionsAdded') : Translation :: get('QuestionsNotAdded');

        $this->redirect($message, ! $succes, array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE));
    }

}
?>