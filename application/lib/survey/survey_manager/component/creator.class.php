<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.survey.survey_manager.component
 */
require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/../../publisher/survey_publisher.class.php';

/**
 * Component to create a new survey_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class SurveyManagerCreatorComponent extends SurveyManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateSurveyPublication')));
        
        $object = Request :: get('object');
        $pub = new RepoViewer($this, array('survey'), true);
        
        if (! isset($object))
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new SurveyPublisher($pub);
            $html[] = $publisher->get_publications_form($object);
        }
        
        $this->display_header($trail);
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>