<?php
/**
 * $Id: comparer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

require_once dirname(__FILE__) . '/browser/doubles_browser/doubles_browser_table.class.php';

/**
 * Repository manager component which can be used to view doubles in the repository
 */
class RepositoryManagerDoublesViewerComponent extends RepositoryManager
{
	private $content_object;
	
	/**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if(isset($id))
        {
        	$this->content_object = $content_object = $this->retrieve_content_object($id);
        	$display = ContentObjectDisplay :: factory($content_object);
        	$html[] = $display->get_full_html();
        	$html[] = '<br />';
        	$html[] = $this->get_detail_table_html();
        }
        else
        {
    	
    		$html[] = $this->get_full_table_html();
        }
        
    	$this->display_header();
    	
    	echo implode("\n", $html);
    	
        $this->display_footer();
    }
    
	private function get_full_table_html()
    {
        $condition = $this->get_full_condition();
        $parameters = $this->get_parameters(true);
        $table = new DoublesBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }
    
    function get_full_condition()
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
    	$conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_RECYCLED));
    	return new AndCondition($conditions);
    }
    
    function get_detail_table_html()
    {
    	$condition = $this->get_detail_condition();
        $parameters = $this->get_parameters(true);
        $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = $this->content_object->get_id();
        $table = new DoublesBrowserTable($this, $parameters, $condition, true);
        return $table->as_html();
    }
    
	function get_detail_condition()
    {
    	$conditions = array();
    	$conditions[] = $this->get_full_condition();
    	$conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_ID, $this->content_object->get_id(), ContentObject :: get_table_name()));
    	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_CONTENT_HASH, $this->content_object->get_content_hash());
    	 
    	return new AndCondition($conditions);
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_doubles_viewer');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }
}
?>