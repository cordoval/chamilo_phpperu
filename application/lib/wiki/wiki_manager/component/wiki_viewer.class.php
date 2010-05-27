<?php
/**
 * $Id: wiki_viewer.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */
require_once dirname(__FILE__) . '/../wiki_manager.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_display.class.php';

class WikiManagerWikiViewerComponent extends WikiManager
{
    private $complex_display;
    private $trail;
    private $content_object;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $this->trail = $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('Wiki')));
        
        $this->set_parameter(WikiManager :: PARAM_ACTION, WikiManager :: ACTION_VIEW_WIKI);
        $this->set_parameter(WikiManager :: PARAM_WIKI_PUBLICATION, Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
        
        $this->complex_display = ComplexDisplay :: factory($this, Wiki :: get_type_name());
        
        $pub = WikiDataManager :: get_instance()->retrieve_wiki_publication(Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
        
        $this->content_object = $pub->get_content_object();
        //$this->display_header($trail, false);
        $this->complex_display->run();
        //$this->display_footer();
    }
    
    function get_root_content_object()
    {
        return $this->content_object;
    }
    
	function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}
    	
    	return parent :: display_header($this->trail);
    }

}
?>