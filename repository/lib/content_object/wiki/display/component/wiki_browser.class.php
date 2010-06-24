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
require_once Path :: get_plugin_path() . 'wiki/mediawiki_parser.class.php';

class WikiDisplayWikiBrowserComponent extends WikiDisplay
{
    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_toolbar($this, $this->get_root_content_object()->get_id(), $this->get_root_content_object(), null);
        $this->get_breadcrumbtrail();

        $this->display_header();

        if ($this->get_root_content_object() != null)
        {
            $complex_wiki_homepage = $this->get_wiki_homepage($this->get_root_content_object_id());

            $html = array();

            // The genereal menu
            $html[] = '<div class="wiki-menu">';

            $html[] = '<div class="wiki-menu-section">';
            $toolbar = new Toolbar(Toolbar :: TYPE_VERTICAL);
            $toolbar->add_item(new ToolbarItem(Translation :: get('MainPage'), Theme :: get_common_image_path() . 'action_home.png', $this->get_url(array(
                    WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $toolbar->add_item(new ToolbarItem(Translation :: get('Contents'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_BROWSE_WIKI)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $toolbar->add_item(new ToolbarItem(Translation :: get('Statistics'), Theme :: get_common_image_path() . 'action_statistics.png', $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_STATISTICS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $html[] = $toolbar->as_html();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';

            $html[] = '<div class="wiki-menu-section">';
            $toolbar = new Toolbar(Toolbar :: TYPE_VERTICAL);
            $toolbar->add_item(new ToolbarItem(Translation :: get('CreateWikiPage'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $html[] = $toolbar->as_html();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';

            $html[] = '</div>';

            // The main content pane
            $html[] = '<div class="wiki-pane">';
            $html[] = '<div class="wiki-pane-actions-bar">';
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-left">';
//
//            $discuss_url = $this->get_url(array(
//                    WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DISCUSS, 'wiki_publication' => Request :: get('wiki_publication'),
//                    ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_homepage->get_id()));
//
//            $statistics_url = $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_PAGE_STATISTICS, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_homepage->get_id()));
//
            $html[] = '<li><a class="current" href="#">Article</a></li>';
//            $html[] = '<li><a href="' . $discuss_url . '">Discuss</a></li>';
//            $html[] = '<li><a href="' . $statistics_url . '">Statistics</a></li>';
            $html[] = '</ul>';
//
//            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-right">';
//
//            $delete_url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_homepage->get_id()));
//            $history_url = $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_homepage->get_id()));
//            $edit_url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_homepage->get_id()));
//
//            $html[] = '<li><a href="#">Read</a></li>';
//            $html[] = '<li><a href="' . $edit_url . '">Edit</a></li>';
//            $html[] = '<li><a href="' . $history_url . '">History</a></li>';
//            $html[] = '<li><a href="' . $delete_url . '">Delete</a></li>';
//            $html[] = '</ul>';

            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';

            $html[] = '<div class="wiki-pane-content">';

            $table = new WikiPageTable($this, $this->get_root_content_object()->get_id());
            $html[] = $table->as_html() . '</div>';

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