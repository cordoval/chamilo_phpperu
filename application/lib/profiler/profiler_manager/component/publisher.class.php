<?php
/**
 * $Id: publisher.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
require_once dirname(__FILE__) . '/../profiler_manager.class.php';
require_once dirname(__FILE__) . '/../profiler_manager_component.class.php';
require_once dirname(__FILE__) . '/../../publisher/profile_publisher.class.php';

class ProfilerManagerPublisherComponent extends ProfilerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('MyProfiler')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishProfile')));
        $trail->add_help('profiler general');
        
        $pub = new RepoViewer($this, 'profile');
        
        if (!$pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new ProfilePublisher($pub);
            $html[] = $publisher->get_publications_form($pub->get_selected_objects());
        }
        
        $this->display_header($trail);
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>