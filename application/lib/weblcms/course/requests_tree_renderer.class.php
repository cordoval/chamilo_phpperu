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
		
    	$menu_item = array();
		$menu_item['class'] = 'type type_request';
		$menu_item['title'] = Translation :: get('Pending');
		$menu_item['description'] = Translation :: get('Pending');
		$menu_item['url'] = $this->parent->get_url(
			Array(WeblcmsManager :: PARAM_REQUEST_TYPE => $request_type, 
			      WeblcmsManager :: PARAM_REQUEST_VIEW => WeblcmsManagerAdminRequestBrowserComponent :: PENDING_REQUEST_VIEW));
		$sub_menu[] = $menu_item;
		
		$menu_item = array();
		$menu_item['class'] = 'type type_request';
		$menu_item['title'] = Translation :: get('Allowed');
		$menu_item['description'] = Translation :: get('Allowed');
		$menu_item['url'] = $this->parent->get_url(
			Array(WeblcmsManager :: PARAM_REQUEST_TYPE => $request_type, 
			      WeblcmsManager :: PARAM_REQUEST_VIEW => WeblcmsManagerAdminRequestBrowserComponent :: ALLOWED_REQUEST_VIEW));
		$sub_menu[] = $menu_item;
		
		$menu_item = array();
		$menu_item['class'] = 'type type_request';
		$menu_item['title'] = Translation :: get('Denied');
		$menu_item['description'] = Translation :: get('Denied');
		$menu_item['url'] = $this->parent->get_url(
			Array(WeblcmsManager :: PARAM_REQUEST_TYPE => $request_type, 
			      WeblcmsManager :: PARAM_REQUEST_VIEW => WeblcmsManagerAdminRequestBrowserComponent :: DENIED_REQUEST_VIEW));
		$sub_menu[] = $menu_item;
		return $sub_menu;
    }
    

	function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
    
    static function get_tree_name()
    {
    	return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }
}