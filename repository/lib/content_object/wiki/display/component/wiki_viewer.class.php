<?php
/**
 * $Id: wiki_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the compenent that allows the user to view all pages of a wiki.
 * If no homepage is set all available pages will be shown, otherwise the homepage will be shown.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__) . '/wiki_page_table/wiki_page_table.class.php';
require_once dirname(__FILE__) . '/../wiki_parser.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_display.class.php';

class WikiDisplayWikiViewerComponent extends WikiDisplay
{
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_toolbar($this, $this->get_root_content_object()->get_id(), $this->get_root_content_object(), null);
        $this->get_breadcrumbtrail();
        
        $this->display_header();
        
        if ($this->get_root_content_object() != null)
        {
            
            $homepage = $this->get_wiki_homepage($this->get_root_content_object_id());
            
            $html = array();
            
            $html[] = '<div class="wiki-menu" style="width: 10%; float: left;">';
            $html[] = $this->action_bar->as_html();
            $html[] = '</div>';
//            $html[] = '<div style="width: 89%; border-left: 1px solid #e5edf9; float: right;">';
            $html[] = '<div class="wiki-pane">';
            
            $html[] = '<div class="wiki-pane-actions-bar">';
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-left">';
            $html[] = '<li><a class="current" href="#">Article</a></li>';
            $html[] = '<li><a href="#">Discuss</a></li>';
            $html[] = '<li><a href="#">Statistics</a></li>';
            $html[] = '</ul>';
            
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-right">';
            $html[] = '<li style="float: right;"><a href="#">Delete</a></li>';
            $html[] = '<li style="float: right;"><a href="#">History</a></li>';
            $html[] = '<li style="float: right;"><a href="#">Edit</a></li>';
            $html[] = '<li style="float: right;"><a href="#">Read</a></li>';
            $html[] = '</ul>';
            
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            
            $html[] = '<div class="wiki-pane-content">';
            
            $complex_wiki_homepage = $this->get_wiki_homepage($this->get_root_content_object_id());
            
            if (! $complex_wiki_homepage)
            {
                $table = new WikiPageTable($this, $this->get_root_content_object()->get_id());
                $html[] = $table->as_html() . '</div>';
            }
            else
            {
                $wiki_homepage = $complex_wiki_homepage->get_ref_object();
                
                $parser = new WikiParser($this, $this->get_root_content_object()->get_id(), $wiki_homepage->get_description(), $complex_wiki_homepage->get_id());
                
                $html[] = '<div class="wiki-pane-content-title">' . $wiki_homepage->get_title() . '</div>';
                $html[] = '<div class="wiki-pane-content-subtitle">From: ' . $this->get_root_content_object()->get_title() . '</div>';
                
                $html[] = $parser->parse_wiki_text();
                $html[] = $parser->get_wiki_text();
                $html[] = '<div class="wiki-pane-top"><a href=#top>' . Theme :: get_common_image('action_ajax_add', 'png', Translation::get('BackToTop')) . '</a></div>';
                //$html[] = '<div class="wiki-pane-top"><a href=#top>' . Theme :: get_common_image('action_ajax_add') . '</a></div>';
            }
            
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
//            $html[] = '<div class="clear"></div>';
//            $html[] = '</div>';
            
            echo implode("\n", $html);
        }
        
//                        echo '<div id="trailbox2" style="padding:0px;">' . $this->get_breadcrumbtrail()->render() . '<br /><br /><br /></div>';
        //                echo '<div style="float:left; width: 135px;">' . $this->action_bar->as_html() . '</div>';
        //
        //                if ($this->get_root_content_object() != null)
        //                {
        //                    $homepage = $this->get_wiki_homepage($this->get_root_content_object_id());
        //                    dump($homepage);
        //                    echo '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">' . $this->get_root_content_object()->get_title() . '</div><hr style="height:1px;color:#4271B5;width:100%;">';
        //                    $table = new WikiPageTable($this, $this->get_root_content_object()->get_id());
        //                    echo $table->as_html() . '</div>';
        //                }
        

        $this->display_footer();
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            return new OrCondition($conditions);
        }
        return null;
    }
}
?>