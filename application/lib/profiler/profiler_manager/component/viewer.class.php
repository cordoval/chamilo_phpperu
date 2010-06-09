<?php
/**
 * $Id: viewer.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
require_once dirname(__FILE__) . '/../profiler_manager.class.php';

class ProfilerManagerViewerComponent extends ProfilerManager
{
    private $folder;
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('MyProfiler')));
        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewProfile')));
        $trail->add_help('profiler general');
        
        $id = Request :: get(ProfilerManager :: PARAM_PROFILE_ID);
        
        if ($id)
        {
            $this->publication = $this->retrieve_profile_publication($id);
            $trail->add(new Breadcrumb($this->get_url(array(ProfilerManager :: PARAM_PROFILE_ID => $id)), $this->publication->get_publication_object()->get_title()));
            
            $this->display_header($trail);
            echo $this->get_publication_as_html();
            
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoProfileSelected')));
        }
    }

    function get_publication_as_html()
    {
        $publication = $this->publication;
        $profile = $publication->get_publication_object();
        
        $display = ContentObjectDisplay :: factory($profile);
        
        $html = array();
        $html[] = $display->get_full_html();
        
        return implode("\n", $html);
    }
}
?>