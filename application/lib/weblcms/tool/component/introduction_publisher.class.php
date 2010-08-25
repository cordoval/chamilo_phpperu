<?php
/**
 * $Id: introduction_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once dirname(__FILE__) . '/../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../publisher/content_object_publisher.class.php';

class ToolIntroductionPublisherComponent extends ToolComponent
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), Translation :: get('PublishIntroductionText')));
        $trail->add_help('courses general');
        
        $repo_viewer = new RepoViewer($this, Introduction :: get_type_name(), RepoViewer :: SELECT_SINGLE);
        $repo_viewer->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_PUBLISH_INTRODUCTION);
        
        if (!$repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $dm = WeblcmsDataManager :: get_instance();
            $do = $dm->get_next_content_object_publication_display_order_index($this->get_course_id(), $this->get_tool_id(), 0);
            
            $pub = new ContentObjectPublication();
            $pub->set_content_object_id($repo_viewer->get_selected_objects());
            $pub->set_course_id($this->get_course_id());
            $pub->set_tool($this->get_tool_id());
            $pub->set_category_id(0);
            $pub->set_target_users(array());
            $pub->set_target_course_groups(array());
            $pub->set_from_date(0);
            $pub->set_to_date(0);
            $pub->set_publisher_id(Session :: get_user_id());
            $pub->set_publication_date(time());
            $pub->set_modified_date(time());
            $pub->set_hidden(0);
            $pub->set_display_order_index($do);
            $pub->set_email_sent(false);
            $pub->set_show_on_homepage(0);
            
            $pub->create();
            
            $parameters = $this->get_parameters();
            $parameters['tool_action'] = null;
            
            $this->redirect(Translation :: get('IntroductionPublished'), (false), $parameters);
        }
    }
}
?>