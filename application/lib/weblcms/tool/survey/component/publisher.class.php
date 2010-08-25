<?php
/**
 * $Id: survey_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../survey_content_object_publisher.class.php';

/**
 * Represents the repo_viewer component for the survey tool.
 */
class SurveyToolPublisherComponent extends SurveyTool
{

    /**
     * Shows the html for this component.
     *
     */
    function run()
    {
        //        if (! $this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        //        {
        //            Display :: not_allowed();
        //            return;
        //        }
        //        
        //        $trail = BreadcrumbTrail :: get_instance();
        //        $trail->add(new Breadcrumb($this->get_url(array(SurveyTool :: PARAM_ACTION => SurveyTool :: ACTION_PUBLISH)), Translation :: get('PublishSurvey')));
        //        $trail->add_help('courses assessment tool');
        //        
        //        $pub = new ContentObjectRepoViewer($this, array(Survey :: get_type_name()));
        //        if (! $pub->is_ready_to_be_published())
        //        {
        //            $html[] = $pub->as_html();
        //        }
        //        else
        //        {
        //            $object_id = $pub->get_selected_objects();
        //            
        //            $publisher = new SurveyContentObjectPublisher($pub);
        //            $html[] = $publisher->get_publications_form($object_id);
        //        }
        //        
        //        $this->display_header();
        //        echo implode("\n", $html);
        //        $this->display_footer();
        

        $component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}

?>