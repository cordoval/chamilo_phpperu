<?php
/**
 * $Id: assessment_merger.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component
 */
require_once dirname(__FILE__) . '/../assessment_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once dirname(__FILE__) . '/assessment_merger/object_browser_table.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/assessment/assessment.class.php';

class AssessmentBuilderAssessmentMergerComponent extends AssessmentBuilderComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id())), $this->get_root_lo()->get_title()));
        $trail->add(new Breadcrumb($this->get_url(array(ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id())), Translation :: get('MergeAssessment')));
        $trail->add_help('repository assessment builder');
        $assessment = $this->get_root_lo();
        
        $object = Request :: get('object');
        $pub = new ComplexRepoViewer($this, 'assessment');
        $pub->set_parameter(ComplexBuilder :: PARAM_ROOT_LO, $assessment->get_id());
        $pub->set_parameter('publish', Request :: get('publish'));
        
        $pub->parse_input();
        
        if (! isset($object))
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $selected_assessment = RepositoryDataManager :: get_instance()->retrieve_content_object($object, 'assessment');
            $display = ContentObjectDisplay :: factory($selected_assessment);
            $bar = $this->get_action_bar($selected_assessment);
            
            //$html[] = '<h3>' . Translation :: get('SelectedAssessment') . '</h3>';
            $html[] = $display->get_full_html();
            $html[] = '<br />';
            $html[] = $bar->as_html();
            $html[] = '<h3>' . Translation :: get('SelectQuestions') . '</h3>';
            
            $params = array(ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), 'publish' => Request :: get('publish'));
            $table = new ObjectBrowserTable($this, array_merge($params, $this->get_parameters()), $this->get_condition($selected_assessment));
            $html[] = $table->as_html();
        }
        
        $this->display_header($trail);
        echo '<br />' . implode("\n", $html);
        $this->display_footer();
    }

    function get_condition($selected_assessment)
    {
        $sub_condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $selected_assessment->get_id());
        $condition = new SubselectCondition(ContentObject :: PROPERTY_ID, ComplexContentObjectItem :: PROPERTY_REF, RepositoryDataManager :: get_instance()->escape_table_name(ComplexContentObjectItem :: get_table_name()), $sub_condition, ContentObject :: get_table_name());
        
        return $condition;
    }

    function get_question_selector_url($question_id, $assessment_id)
    {
        return $this->get_url(array(AssessmentBuilder :: PARAM_BUILDER_ACTION => AssessmentBuilder :: ACTION_SELECT_QUESTIONS, AssessmentBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), 'publish' => Request :: get('publish'), AssessmentBuilder :: PARAM_QUESTION_ID => $question_id, AssessmentBuilder :: PARAM_ASSESSMENT_ID => $assessment_id));
    }

    function get_action_bar($selected_assessment)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddAllQuestions'), Theme :: get_common_image_path() . 'action_add.png', $this->get_question_selector_url(null, $selected_assessment->get_id())));
        
        return $action_bar;
    }
}

?>
