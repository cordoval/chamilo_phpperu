<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class ComplexBuilderBrowserComponent extends ComplexBuilderComponent
{

    function run()
    {
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));
        //$trail->merge($menu_trail);
        $trail->add_help('repository builder');

        if ($this->get_complex_content_object_item())
        {
            $content_object = $this->get_complex_content_object_item()->get_ref_object();
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }

        $this->display_header();

        $action_bar = $this->get_action_bar($content_object);

        if ($action_bar)
        {
            echo '<br />';
            echo $action_bar;
        }

        $display = ContentObjectDisplay :: factory($this->get_root_content_object());
        echo $display->get_full_html();

        echo '<br />';
        echo $this->get_creation_links($content_object);
        echo '<div class="clear">&nbsp;</div><br />';

        if ($this->get_parent()->show_menu())
        {
            echo '<div style="width: 18%; overflow: auto; float: left;">';
            echo $this->get_complex_content_object_menu();
            echo '</div>';
            echo '<div style="width: 80%; float: right;">';
        }
        else
        {
            echo '<div>';
        }

        echo $this->get_complex_content_object_table_html();
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';

        $this->display_footer();
    }
}

?>