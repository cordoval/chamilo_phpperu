<?php
/**
 * $Id: request_tree_renderer.class.php 204 2009-11-13 12:51:30Z tristan $
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

class RequestsTreeRenderer extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    
    private $parent;
    private $current_item = 1;
    
    function RequestsTreeRenderer($parent)
    {
		$this->parent = $parent;
        $menu = $this->get_menu_items();
        parent :: __construct($menu);
    }

    private function get_menu_items()
    {
        $menu = array();
        $menu_item = array();
		$menu_item['class'] = 'type type_request';
		$menu_item['title'] = Translation :: get('Requests');
		$menu_item['description'] = Translation :: get('Requests');    
		$menu_item['url'] = '#';      
		$menu_item['sub'] = $this->get_requests_array();
        $menu[] = $menu_item;
        return $menu;
    }
    
    private function get_requests_array()
    {
    	$sub_menu = array();
		
    	$menu_item = array();
		$menu_item['class'] = 'type type_request';
		$menu_item['title'] = Translation :: get('CreationRequests');
		$menu_item['description'] = Translation :: get('CreationRequests');  
		$menu_item['url'] = '#';       
		$menu_item['sub'] = $this->get_sub_division(CommonRequest :: CREATION_REQUEST);
		$sub_menu[] = $menu_item;
    	
    	$menu_item = array();
		$menu_item['class'] = 'type type_request';
		$menu_item['title'] = Translation :: get('SubscriptionRequests');
		$menu_item['description'] = Translation :: get('SubscriptionRequests'); 
		$menu_item['url'] = '#';      
		$menu_item['sub'] = $this->get_sub_division(CommonRequest :: SUBSCRIPTION_REQUEST);
		$sub_menu[] = $menu_item;
		return $sub_menu;
    }
    
    private function get_sub_division($request_type)
    {
       	$sub_menu = array();
		
       	$request_database_method = null;
        switch($request_type)
        {
        	case CommonRequest :: SUBSCRIPTION_REQUEST: $request_database_method = 'count_requests'; break;
        	case CommonRequest :: CREATION_REQUEST: $request_database_method = 'count_course_create_requests'; break;
        }
       	
       	$request_view = null;
       	$translation = null;
       	$condition = null;
       	       	
       	for($i = 0; $i < 3; $i++)
       	{
       		switch($i)
       		{
       			case 0: $translation = 'Pending';
       					$request_view = WeblcmsManagerAdminRequestBrowserComponent :: PENDING_REQUEST_VIEW;
       					$condition = new EqualityCondition(CommonRequest :: PROPERTY_DECISION, CommonRequest :: NO_DECISION);
       					break;
       			case 1: $translation = 'Allowed';
  						$request_view = WeblcmsManagerAdminRequestBrowserComponent :: ALLOWED_REQUEST_VIEW;
  						$condition = new EqualityCondition(CommonRequest :: PROPERTY_DECISION, CommonRequest :: ALLOWED_DECISION);
  						break;
       			case 2: $translation = 'Denied';
       					$request_view = WeblcmsManagerAdminRequestBrowserComponent :: DENIED_REQUEST_VIEW;
       					$condition = new EqualityCondition(CommonRequest :: PROPERTY_DECISION, CommonRequest :: DENIED_DECISION);
       					break;
       		}
       		
       		$count =  $this->parent->$request_database_method($condition);
       	
	    	$menu_item = array();
			$menu_item['class'] = 'type type_request';
			$menu_item['title'] = Translation :: get($translation) . ' (' . $count . ')';
			$menu_item['description'] = Translation :: get($translation);
			$menu_item['url'] = $this->parent->get_url(
			Array(WeblcmsManager :: PARAM_REQUEST_TYPE => $request_type, 
			      WeblcmsManager :: PARAM_REQUEST_VIEW => $request_view));
			$this->check_selected_item($menu_item, $request_type, $request_view);
			$sub_menu[] = $menu_item;
       	}
		return $sub_menu;
    }

	function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
    
    function check_selected_item($menu_item, $request_type, $request_view)
    {
    	if($request_type == $this->parent->get_request_type() && $request_view == $this->parent->get_request_view())
    		$this->forceCurrentUrl($menu_item['url']);
    }
    
    static function get_tree_name()
    {
    	return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }
}