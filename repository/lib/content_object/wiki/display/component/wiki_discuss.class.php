<?php
/**
 * $Id: wiki_discuss.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the discuss page. Here a user can add feedback to a wiki_page.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_parser.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_display.class.php';

class WikiDisplayWikiDiscussComponent extends WikiDisplay
{
    private $action_bar;
    private $wiki_page_id;
    private $complex_id;
    private $feedback_id;
    private $links;
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';

    function run()
    {
   		$this->action_bar = $this->get_toolbar($this, $this->get_root_content_object_id(), $this->get_root_content_object(), $this->get_selected_complex_content_object_item());

   		$this->set_parameter(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_selected_complex_content_object_item_id());
   		
    	$feedback_manager = new FeedbackManager($this, $this->get_application_name(), $this->get_publication()->get_id(), $this->get_selected_complex_content_object_item_id());
   		$feedback_manager->run();
    }
    
    function add_actionbar_item($item)
    {
    	$this->action_bar->add_common_action($item);
    }
    
    function display_header()
    {
    	parent :: display_header();
    	
    	$selected_complex_content_object_item = $this->get_selected_complex_content_object_item();
    	$content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($selected_complex_content_object_item->get_ref());
    	
    	echo '<div style="float:left; width: 135px;">' . $this->action_bar->as_html() . '</div>';
        echo '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">' . Translation :: get('DiscussThe') . ' ' . $content_object->get_title() . ' ' . Translation :: get('Page') . '<hr style="height:1px;color:#4271B5;width:100%;"></div>';
    	
    	$display = ContentObjectDisplay :: factory($content_object);
    	$parser = new WikiParser($this, $this->get_root_content_object_id(), $display->get_full_html(), $this->get_selected_complex_content_object_item_id());
        $parser->parse_wiki_text();
        
        echo '<div id="content">' . $parser->get_wiki_text() . '</div><br />';
    }
    
	function display_footer()
    {
    	echo '</div>';
    	return parent :: display_footer();
    }

}

?>