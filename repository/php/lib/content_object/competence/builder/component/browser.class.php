<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator.component
 */
//require_once dirname(__FILE__) . '/../indicator_builder_component.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class CompetenceBuilderBrowserComponent extends CompetenceBuilder
{

    function run()
    {
        //$object = $this->get_root_content_object();
        ComplexBuilderComponent :: launch($this);
        
        /*
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));
        $trail->add(new Breadcrumb($this->get_url(array(ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT => $object->get_id())), $object->get_title()));
        $trail->add_help('repository indicator builder');
        
        $this->display_header($trail);
        */
//        $action_bar = $this->get_action_bar($object);
//        
//        echo '<br />';
//        if ($action_bar)
//        {
//            echo $action_bar->as_html();
//            echo '<br />';
//        }
        /*
        $display = ContentObjectDisplay :: factory($this->get_root_content_object());
        echo $display->get_full_html();
        
        echo '<br />';
        echo $this->get_creation_links($object);
        echo '<div class="clear">&nbsp;</div><br />';
        
        echo $this->get_complex_content_object_table_html(false);
        
        $this->display_footer();*/
    }
}

?>