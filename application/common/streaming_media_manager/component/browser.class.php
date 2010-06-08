<?php
require_once dirname(__FILE__) . '/streaming_media_browser_table/streaming_media_browser_table.class.php';
require_once dirname(__FILE__) . '/../forms/streaming_media_search_form.class.php';

class StreamingMediaBrowserComponent extends StreamingMediaComponent
{
    private $menu;
    private $form;

    function StreamingMediaBrowserComponent($application)
    {
        parent :: __construct($application);
        $this->form = new StreamingMediaSearchForm($this->get_url());
    }

    function get_menu()
    {
        return $this->menu;
    }

    function set_menu($menu)
    {
        $this->menu = $menu;
    }

    function render_menu()
    {
    	$extra = $this->get_menu_items();
    	if ($this->form->get_query())
        {
            $search_url = '#';
            $search = array();

            $search['title'] = Translation :: get('SearchResults');

            $search['url'] = $search_url;
            $search['class'] = 'search_results';
            $extra[] = $search;
        }
        else
        {
            $search_url = null;
        }
    	
    	$this->menu = new StreamingMediaMenu(Request :: get(StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID), $this->get_parent(), $extra);
    	
    	if ($search_url)
        {
            $this->menu->forceCurrentUrl($search_url);
        }
        
        $html = array();
        if ($this->menu->count_menu_items() > 0)
        {        
	        $html[] = '<div style=" width: 20%; overflow: auto; float: left">';
	        $html[] = $this->menu->render_as_tree();
	        $html[] = '</div>';
        }
        return implode("\n", $html);
    }

    function run()
    {
        $query = $this->form->get_query();
        $html = array();
        
        if (isset($query) && $query != '')
        {
            $this->set_parameter(StreamingMediaSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, $query);
        }
        
        $streaming_media_objects = $this->retrieve_streaming_media_objects();
        $this->display_header();
        
        if ($this->get_menu() == null)
        {
            $html[] = $this->render_menu();
        }
        if ($this->menu->count_menu_items() > 0)
        {
        	$html[] = '<div style=" width: 80%; overflow: auto; float: center">';
        }

        $html[] = '<div class="search_form" style="float: right; margin: 0px 0px 5px 0px;">';
        $html[] = $this->form->as_html();
        $html[] = '</div>';

        $browser_table = new StreamingMediaBrowserTable($this, $this->get_parameters(), $this->get_condition());
        $html[] = $browser_table->as_html();
        
        if ($this->menu->count_menu_items() > 0)
        {
        	$html[] = '</div>';
        }
        echo (implode("\n", $html));
        $this->display_footer();
    }

    function get_condition()
    {
        $query = $this->form->get_query();
        if (isset($query) && $query != '')
        {
            return $this->translate_search_query($query);
        }
        return null;
    }
}
?>