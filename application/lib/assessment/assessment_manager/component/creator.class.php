<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../publisher/assessment_publisher.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerCreatorComponent extends AssessmentManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateAssessmentPublication')));
        
        $object = Request :: get('object');
        $pub = new RepoViewer($this, array('assessment', 'survey', 'hotpotatoes'), true);
        
        if (! isset($object))
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new AssessmentPublisher($pub);
            $html[] = $publisher->get_publications_form($object);
        }
        
        $this->display_header($trail);
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>