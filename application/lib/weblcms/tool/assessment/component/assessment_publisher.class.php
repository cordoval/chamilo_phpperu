<?php
/**
 * $Id: assessment_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

/**
 * Represents the repo_viewer component for the assessment tool.
 */
class AssessmentToolPublisherComponent extends AssessmentToolComponent
{

    /**
     * Shows the html for this component.
     *
     */
    function run()
    {
        if (! $this->is_allowed(ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH)), Translation :: get('PublishAssessment')));
        $trail->add_help('courses assessment tool');
        
        $pub = new ContentObjectRepoViewer($this, array(Assessment :: get_type_name(), Survey :: get_type_name(), Hotpotatoes :: get_type_name()));
        
        if (! $pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $object_id = $pub->get_selected_objects();
            
            $publisher = new ContentObjectPublisher($pub);
            $html[] = $publisher->get_publications_form($object_id);
        }
        
        $this->display_header($trail, true);
        echo implode("\n", $html);
        $this->display_footer();
    }
}

?>