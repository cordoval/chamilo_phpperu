<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.wiki.component
 */
require_once Path :: get_repository_path() . '/lib/content_object/wiki/wiki.class.php';
require_once dirname(__FILE__) . '/browser/wiki_browser_table_cell_renderer.class.php';

class WikiBuilderBrowserComponent extends WikiBuilder
{

    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent::BROWSER_COMPONENT, $this);
        
        $browser->run();
        /*
        $wiki = $this->get_root_content_object();
        
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), $wiki->get_title()));
        $trail->add_help('repository wiki builder');
        
        $this->display_header($trail);
        $action_bar = $this->get_action_bar($wiki);
        
        echo '<br />';
        if ($action_bar)
        {
            echo $action_bar->as_html();
            echo '<br />';
        }
        
        $display = ContentObjectDisplay :: factory($wiki);
        echo $display->get_full_html();
        
        echo '<br />';
        echo $this->get_creation_links($wiki);
        echo '<div class="clear">&nbsp;</div><br />';
        
        echo $this->get_complex_content_object_table_html(false, null, new WikiBrowserTableCellRenderer($this->get_parent(), $this->get_complex_content_object_table_condition()));
        
        $this->display_footer();*/
    }
}

?>