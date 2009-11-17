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
        if (! $this->is_allowed(ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), Translation :: get('PublishIntroductionText')));
        $trail->add_help('courses general');
        /*$pub = new ContentObjectPublisher($this, 'introduction', true);

		$html[] = '<p><a href="' . $this->get_url() . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();*/
        
        $object = Request :: get('object');
        
        $pub = new ContentObjectRepoViewer($this, 'introduction', true);
        $pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_PUBLISH_INTRODUCTION);
        
        if (! isset($object))
        {
            $html[] = '<p><a href="' . $this->get_url() . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
            $html[] = $pub->as_html();
        }
        else
        {
            $dm = WeblcmsDataManager :: get_instance();
            $do = $dm->get_next_content_object_publication_display_order_index($this->get_course_id(), $this->get_tool_id(), 0);
            
            $obj = new ContentObject();
            $obj->set_id($object);
            
            $pub = new ContentObjectPublication();
            $pub->set_content_object_id($object);
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
        
        $this->display_header($trail, true);
        echo implode("\n", $html);
        $this->display_footer();
    }
}
?>