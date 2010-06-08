<?php
/**
 * $Id: browser.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.validation_manager.component
 */

/**
 * Description of browserclass
 *
 * @author Pieter Hens
 */

require_once dirname(__FILE__) . '/validation_browser/validation_browser_table.class.php';

class ValidationManagerBrowserComponent extends ValidationManagerComponent
{
    
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';
    
    private $pid;
    private $cid;
    private $user_id;
    private $action;

    function run()
    {
    	$html = $this->as_html();
    	
    	$this->display_header(new BreadcrumbTrail());
    	echo $html;
    	$this->display_footer();
    }
    
    function as_html()
    {
        
        $this->pid = Request :: get('pid');
        $this->user_id = Request :: get('user_id');
        $this->cid = Request :: get('cid');
        $this->action = Request :: get('action');
        $application = $this->get_parent()->get_application();
        
        $url = $this->get_url(array('pid' => $this->pid, 'cid' => $this->cid, 'user_id' => $this->user_id, 'action' => $this->action));
        
        $table = new ValidationBrowserTab($this, null, $parameters, $this->get_condition());
        
        return $table->as_html();
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_PID, $this->pid);
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_CID, $this->cid);
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_APPLICATION, PortfolioManager :: APPLICATION_NAME);
        $condition = new AndCondition($conditions);
        return $condition;
    }

    function get_publication_deleting_url($validation)
    {
        return $this->get_parent()->get_publication_deleting_url($validation);
    }

}
?>