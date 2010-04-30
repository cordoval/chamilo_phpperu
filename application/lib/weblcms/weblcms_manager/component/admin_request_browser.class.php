<?php
/**
 * $Id: admin_course_type_browser.class.php 218 2010-03-11 14:21:26Z Yannick & Tristan $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/admin_request_browser/admin_request_browser_table.class.php';
require_once dirname(__FILE__) . '/../../course/requests_tree_renderer.class.php';
/**
 * Weblcms component which allows the the platform admin to browse the request
 */
class WeblcmsManagerAdminRequestBrowserComponent extends WeblcmsManager
{
	
	const PENDING_REQUEST_VIEW = 'pending_request_view';
	const ALLOWED_REQUEST_VIEW = 'allowed_request_view';
	const DENIED_REQUEST_VIEW = 'denied_request_view';
	
    private $action_bar;
	private $request_type;
	private $request_view;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $this->request_type = Request :: get(WeblcmsManager :: PARAM_REQUEST_TYPE);
        $this->request_view = Request :: get(WeblcmsManager :: PARAM_REQUEST_VIEW);
        
        if(is_null($this->request_type))
        	$this->request_type = CommonRequest :: CREATION_REQUEST;
        if(is_null($this->request_view))
        	$this->request_view = self :: PENDING_REQUEST_VIEW;
        
        $trail = new BreadcrumbTrail();
        if ($this->get_user()->is_platform_admin())
        {
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }
        else
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Requests')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Requests')));
        $trail->add_help('Request general');
              
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $this->display_header($trail, false, true);
        $this->action_bar = $this->get_action_bar();
        echo $this->get_request_html();
        $this->display_footer();
    }

    function get_request_html()
    {    
        $html = array();
        $menu = new RequestsTreeRenderer($this);
        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->action_bar->as_html() . '<br />';
        $html[] = '<div style="float: left; padding-right: 20px; width: 18%; overflow: auto; height: 100%;">' . $menu->render_as_tree() . '</div>';
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $this->get_table_html();
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        return implode($html, "\n");
    }
    
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->set_search_url($this->get_url());
		//$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_CREATOR)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
	  
	function get_table_html()
	{
		$parameters = array();
		$parameters[WeblcmsManager :: PARAM_ACTION] = WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER;
		$parameters[WeblcmsManager :: PARAM_REQUEST_TYPE] = $this->request_type;

		$table = new AdminRequestBrowserTable($this, $parameters, $this->get_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}
    
    function get_condition()
    {
        $query = $this->action_bar->get_query();
		
        $conditions = array();
        
        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(CommonRequest :: PROPERTY_MOTIVATION, '*' . $query . '*');            
            $conditions[] = new PatternMatchCondition(CommonRequest :: PROPERTY_TITLE, '*' . $query . '*');
            //$conditions[] = new PatternMatchCondition(CourseType :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            
           	$search_conditions = new OrCondition($conditions);
        }
               
        if (count($search_conditions))
           $conditions[] = $search_conditions;
        
        switch($this->request_view)
        {
        	case self :: PENDING_REQUEST_VIEW: $conditions[] = new EqualityCondition(CommonRequest :: PROPERTY_DECISION, NULL);
        									break;
        	case self :: ALLOWED_REQUEST_VIEW: $conditions[] = new EqualityCondition(CommonRequest :: PROPERTY_DECISION, CommonRequest :: ALLOWED_DECISION);
        									   break;
        	case self :: DENIED_REQUEST_VIEW: $conditions[] = new EqualityCondition(CommonRequest :: PROPERTY_DECISION, CommonRequest :: DENIED_DECISION);
        									   break;
        }
        
        $condition = null;
       	if(count($conditions)>1)
       		$condition = new AndCondition($conditions);
       	else if(count($conditions)==1)
       		$condition = $conditions[0];
       		
        return $condition;
    }
    
    function get_request_type()
    {
    	return $this->request_type;
    }
    
    function get_request_view()
    {
    	return $this->request_view;
    }
}
?>