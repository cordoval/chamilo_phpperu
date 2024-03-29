<?php
namespace repository\content_object\survey_page;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\SubselectCondition;

use common\extensions\repo_viewer\RepoViewerInterface;
use common\extensions\repo_viewer\RepoViewer;

use repository\ComplexBuilder;
use repository\RepositoryDataManager;
use repository\ContentObjectDisplay;
use repository\ComplexContentObjectItem;
use repository\ContentObject;

/**
 * $Id: assessment_merger.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component
 */
require_once dirname(__FILE__) . '/survey_page_merger/object_browser_table.class.php';

class SurveyPageBuilderMergerComponent extends SurveyPageBuilder implements RepoViewerInterface
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(
                ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE)), $this->get_root_content_object()->get_title()));
        $trail->add(new Breadcrumb($this->get_url(array()), Translation :: get('MergeAssessment')));
        $trail->add_help('repository assessment builder');
        $assessment = $this->get_root_content_object();

        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_maximum_select(RepoViewer :: SELECT_SINGLE);
            $repo_viewer->set_parameter(RepoViewer :: PARAM_ID, Request :: get(RepoViewer :: PARAM_ID));

            $repo_viewer->get_parent()->parse_input_from_table();
            $repo_viewer->run();
        }
        else
        {
            $selected_assessment = RepositoryDataManager :: get_instance()->retrieve_content_object(RepoViewer :: get_selected_objects(), SurveyPage :: get_type_name());
            $display = ContentObjectDisplay :: factory($selected_assessment);
            $bar = $this->get_action_bar($selected_assessment);

            //$html[] = '<h3>' . Translation :: get('SelectedAssessment') . '</h3>';
            $html[] = $display->get_full_html();
            $html[] = '<br />';
            $html[] = $bar->as_html();
            $html[] = '<h3>' . Translation :: get('SelectQuestions') . '</h3>';

            $params = array(RepoViewer :: PARAM_ID => Request :: get(RepoViewer :: PARAM_ID));
            $table = new ObjectBrowserTable($this, array_merge($params, $this->get_parameters()), $this->get_condition($selected_assessment));
            $html[] = $table->as_html();

            $this->display_header($trail);
            echo '<br />' . implode("\n", $html);
            $this->display_footer();
        }
    }

    function get_condition($selected_assessment)
    {
        $sub_condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $selected_assessment->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, ComplexContentObjectItem :: PROPERTY_REF, ComplexContentObjectItem :: get_table_name(), $sub_condition, ContentObject :: get_table_name());

        return $condition;
    }

    function get_question_selector_url($question_id, $assessment_id)
    {
        return $this->get_url(array(
                self :: PARAM_BUILDER_ACTION => self :: ACTION_SELECT_QUESTIONS,
                self :: PARAM_QUESTION_ID => $question_id,
                self :: PARAM_SURVEY_PAGE_ID => $assessment_id,
                RepoViewer :: PARAM_ID => Request :: get(RepoViewer :: PARAM_ID)));
    }

    function get_action_bar($selected_assessment)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddAllQuestions'), Theme :: get_common_image_path() . 'action_add.png', $this->get_question_selector_url(null, $selected_assessment->get_id())));

        return $action_bar;
    }

    function get_allowed_content_object_types()
    {
        return array(SurveyPage :: get_type_name());
    }
}

?>