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
        
        if ($this->get_root_content_object() != null)
        {
            $this->display_header();
            
            $table = new WikiPageTable($this, $this->get_root_content_object()->get_id());
            echo $table->as_html();
            
            $this->display_footer();
        }
    }

    function get_condition()
    {
        $query = $this->get_search_form()->get_query();
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