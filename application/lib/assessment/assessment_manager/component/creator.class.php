<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/assessment_publisher.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author
 */
class AssessmentManagerCreatorComponent extends AssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateAssessmentPublication')));

        $pub = new RepoViewer($this, array(Assessment :: get_type_name(), Survey :: get_type_name(), Hotpotatoes :: get_type_name()));

        if (!$pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new AssessmentPublisher($this);
            $html[] = $publisher->get_publications_form($pub->get_selected_objects());
        }

        $this->display_header($trail);
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>