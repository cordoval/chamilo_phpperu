<?php
/**
 * $Id: comparer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

require_once dirname(__FILE__) . '/browser/doubles_browser/doubles_browser_table.class.php';

/**
 * Repository manager component which can be used to view doubles in the repository
 */
class RepositoryManagerDoublesViewerComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $table = $this->get_table_html();
        
    	$this->display_header(new BreadcrumbTrail());
    	echo $table;
        $this->display_footer();
    }
    
	private function get_table_html()
    {
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $table = new DoublesBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }
    
    function get_condition()
    {
    	return new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
    }
}
?>