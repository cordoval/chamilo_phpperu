<?php
/**
 * $Id: wiki_item_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This viewer will show the selected wiki_page.
 * You'll be redirected here from the wiki_viewer page by clicking on the name of a wiki_page
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

//require_once dirname(__FILE__) . '/../wiki_tool.class.php';
//require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__) . '/wiki_page_table/wiki_page_table.class.php';
require_once dirname(__FILE__) . '/../wiki_parser.class.php';

class WikiDisplayWikiItemViewerComponent extends WikiDisplay
{
    private $action_bar;
    private $complex_id;
    private $wiki_page;

    function run()
    {
        /*
         * publication and complex object id are requested.
         * These are used to retrieve
         *  1) the complex object ( reference is stored )
         *  2) the learning object ( actual inforamation about a wiki_page is stored here )
         *
         */
        $this->complex_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $data_manager = RepositoryDataManager :: get_instance();
        
        /*
        *  If a complex object id is passed, the object will be retrieved
        */
        if (! empty($this->complex_id))
        {
            $complex_content_object_item = $data_manager->retrieve_complex_content_object_item($this->complex_id);
            $this->wiki_page = $data_manager->retrieve_content_object($complex_content_object_item->get_ref());
        }
        
        $this->display_header();
        
        $this->action_bar = $this->get_toolbar($this, Request :: get('pid'), $this->get_root_content_object(), $this->complex_id); //$this->get_toolbar();
        //echo '<div id="trailbox2" style="padding:0px;">' . $this->get_breadcrumbtrail()->render() . '<br /><br /><br /></div>';
        echo '<div style="float:left; width: 135px;">' . $this->action_bar->as_html() . '</div>';
        
        echo '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">' . $this->wiki_page->get_title() . '</div><hr style="height:1px;color:#4271B5;width:100%;">';
        
        /*
         *  Here we create the wiki_parser component.
         *  For more information about the parser, please read the information provided in the wiki_parser class
         */
        $parser = new WikiParser($this, $this->get_root_content_object()->get_id(), $this->wiki_page->get_description(), $this->complex_id);
        echo $parser->parse_wiki_text();
        echo $parser->get_wiki_text();
        /*
         * If you don't want the bottom link to show, put the next line in comment
         */
        echo '<div ><a href=#top>' . 'back to top' . '</a></div>';
        echo '</div>';
        
        $this->display_footer();
    }
}
?>