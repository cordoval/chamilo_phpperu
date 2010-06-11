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
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('repository publications');
        
        $output = $this->get_publications_html();
        
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyPublications')));
        $this->display_header($trail, false, true);
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
}
?>