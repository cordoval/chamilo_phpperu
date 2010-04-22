<?php
/**
 * $Id: introduction_publisher.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../content_object_repo_viewer.class.php';

class WeblcmsManagerIntroductionPublisherComponent extends WeblcmsManager
{

    function run()
    {
        /*if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}*/
        
        $trail = new BreadcrumbTrail();
        
		$title = CourseLayout :: get_title($this->get_course());
        
        $trail->add(new Breadcrumb($this->get_url(array('go' => null, 'course' => null)), Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE)), $title));
        $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_PUBLISH_INTRODUCTION)), Translation :: get('PublishIntroduction')));
        
        $trail->add_help('courses general');
        
        $pub = new ContentObjectRepoViewer($this, Introduction :: get_type_name(), RepoViewer :: SELECT_SINGLE);
        
        if (!$pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $objects = $pub->get_selected_objects();
            
        	if(!is_array($objects))
            {
            	$objects = array($objects);
            }
            
        	$dm = WeblcmsDataManager :: get_instance();
        	
        	foreach($objects as $object_id)
        	{
	            $do = $dm->get_next_content_object_publication_display_order_index($this->get_course_id(), $this->get_tool_id(), 0);
	            
	            $pub = new ContentObjectPublication();
	            $pub->set_content_object_id($object_id);
	            $pub->set_course_id($this->get_course_id());
	            $pub->set_tool('introduction');
	            $pub->set_publisher_id(Session :: get_user_id());
	            $pub->set_publication_date(time());
	            $pub->set_modified_date(time());
	            $pub->set_hidden(false);
	            $pub->set_display_order_index($do);
	            $pub->create();
        	}
            
            $parameters = $this->get_parameters();
            $parameters['go'] = WeblcmsManager :: ACTION_VIEW_COURSE;
            
            $this->redirect(Translation :: get('IntroductionPublished'), (false), $parameters);
        }
        
        $this->display_header($trail, false, true);
        echo '<div class="clear"></div><br />';
        echo implode("\n", $html);
        $this->display_footer();
    }
}
?>