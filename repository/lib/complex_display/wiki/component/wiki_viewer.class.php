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
require_once Path :: get_repository_path() . 'lib/complex_display/wiki/wiki_display.class.php';

class WikiDisplayWikiViewerComponent extends WikiDisplayComponent
{
    private $action_bar;

    function run()
    {
        $dm = RepositoryDataManager :: get_instance();

        $this->action_bar = $this->get_parent()->get_toolbar($this, $this->get_root_lo()->get_id(), $this->get_root_lo(), null);
        echo '<div id="trailbox2" style="padding:0px;">' . $this->get_parent()->get_breadcrumbtrail()->render() . '<br /><br /><br /></div>';
        echo '<div style="float:left; width: 135px;">' . $this->action_bar->as_html() . '</div>';

        if ($this->get_root_lo() != null)
        {
            echo '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">' . $this->get_root_lo()->get_title() . '</div><hr style="height:1px;color:#4271B5;width:100%;">';
            $table = new WikiPageTable($this, $this->get_root_lo()->get_id());
            echo $table->as_html() . '</div>';
        }
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