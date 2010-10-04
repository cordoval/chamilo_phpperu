<?php
/**
 * $Id: publication_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which displays user's publications.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RepositoryManagerPublicationBrowserComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $output = $this->get_publications_html();
        
        $this->display_header(null, false, true);
        echo $output;
        $this->display_footer();
    }

    /**
     * Gets the  table which shows the users's publication
     */
    private function get_publications_html()
    {
        
        $condition = $this->get_search_condition();
        $parameters = $this->get_parameters(true);
        $types = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE);
        if (is_array($types) && count($types))
        {
            $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $types;
        }
        $table = new PublicationBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_publication_browser');
    }
}
?>