<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum_topic.component
 */
require_once dirname(__FILE__) . '/../forum_topic_builder_component.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/forum_topic/forum_topic.class.php';

class ForumTopicBuilderBrowserComponent extends ForumTopicBuilderComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail(false);
        //$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));
        $trail->add(new Breadcrumb($this->get_url(array(ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id())), $this->get_root_lo()->get_title()));
        $trail->add_help('repository forum_topic builder');
        
        $this->display_header($trail);
        $forum_topic = $this->get_root_lo();
        $action_bar = $this->get_action_bar($forum_topic);
        
        echo '<br />';
        if ($action_bar)
        {
            echo $action_bar->as_html();
            echo '<br />';
        }
        
        $display = ContentObjectDisplay :: factory($this->get_root_lo());
        echo $display->get_full_html();
        
        echo '<br />';
        echo $this->get_creation_links($forum_topic);
        echo '<div class="clear">&nbsp;</div><br />';
        
        echo $this->get_clo_table_html();
        
        $this->display_footer();
    }
}

?>