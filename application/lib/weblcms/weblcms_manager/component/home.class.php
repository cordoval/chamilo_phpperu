<?php
/**
 * $Id: home.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_user_category.class.php';
/**
 * Weblcms component which provides the user with a list
 * of all courses he or she has subscribed to.
 */
class WeblcmsManagerHomeComponent extends WeblcmsManagerComponent
{

	const MIXED = 0;
	const SEPERATED = 1;
	const OPEN_ONLY = 2;
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {   	
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyCourses')));
        $trail->add_help('courses general');
               
        $this->display_header($trail, false, true);
        echo '<div class="clear"></div>';  
             
        echo $this->display_menu();
              
        echo '<div id="tool_browser_right">'; 
	    
        echo $this->get_active_course_type_tabs();
	    			
        echo '</div>';
        
        $this->display_footer();
    }
            
    function get_active_course_type_tabs()
   	{       
        $tabs = array();
        $courses = array();
        $html = array();      	 	
       	$total = 0;	

       	$course_active_types = $this->retrieve_active_course_types();
       	while($course_type = $course_active_types->next_result())
       	{
       		$conditions = array();
       		$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id(), CourseUserRelation :: get_table_name());
       		$conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course_type->get_id());
       		$condition = new AndCondition($conditions);
       		$courses_result = $this->retrieve_user_courses($condition);
       	 	if($courses_result->size() > 0)
       	 	{
				$tabs[$course_type->get_id()][0] = $courses_result;
				$tabs[$course_type->get_id()][1] = $course_type->get_name();
       	 	}
       	}

   	    $conditions = array();
        $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id(), CourseUserRelation :: get_table_name());
        $conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, 0);
       	$condition = new AndCondition($conditions);
       	$courses_result = $this->retrieve_user_courses($condition);
       	if($courses_result->size() > 0)
       	{
       		$tab_name = Translation :: get('NoCourseTypeTab');
       		if(count($tabs) == 0) $tab_name = null;
			$tabs[0][0] = $courses_result;
			$tabs[0][1] = $tab_name;
       	 }
       	 	
		if(count($tabs) == 0)
        	$this->display_message(Translation :: get('NoCoursesFound'));
        else
        {
        	$html[] = '<div id="admin_tabs">';
       	 	$html[] = '<ul>';
       	 			
       	 	foreach($tabs as $index => $tab)
			{
				if(!is_null($tab))
				{								
	      			$html[] = '<li><a href="#admin_tabs-'.$index.'">';
	          		$html[] = '<span class="category">';
	        		$html[] = '<span class="title">'.$tab[1].'</span>';
	        		$html[] = '</span>';
	        		$html[] = '</a></li>';
				}
			}
        	$html[] = '</ul>';

        	foreach($tabs as $index => $tab)
        	{
        		$html[] = '<div class="admin_tab" id="admin_tabs-'.$index.'">';
        		$html[] = $this->display_courses($tab[0]);
        		$html[] = '<div class="clear"></div>';
        		$html[] = '</div>';
        	}
        	
        	$html[] = '</div>';
        	$html[] = '<script type="text/javascript">';
        	$html[] = '  var tabnumber = ' . $selected_tab . ';';
        	$html[] = '</script>';

        	$html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/admin_ajax.js' . '"></script>';
        }      
        return implode($html, "\n");     
    }

    function display_menu()
    {
        $html = array();
        
        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_right">';
        
        $html[] = '<div id="tool_bar_hide_container" class="hide">';
        $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_right_hide.png" /></a>';
        $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_right_show.png" /></a>';
        $html[] = '</div>';
        
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';
        
        if ($this->get_user()->is_platform_admin())
        {
            $html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . Translation :: get('CourseManagement') . '</li><br />';
            $html[] = $this->display_platform_admin_course_list_links();
            $html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
        }
        else
        {
            $display_add_course_link = $this->get_user()->is_teacher() && ($_SESSION["studentview"] != "studentenview");
            if ($display_add_course_link)
            {
                $html[] = '<li class="tool_list_menu" style="font-weight: bold">' . Translation :: get('MenuUser') . '</li><br />';
                $html[] = $this->display_create_course_link();
            }
        }
        
        $html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . Translation :: get('UserCourseManagement') . '</li><br />';
        $html[] = $this->display_edit_course_list_links();
        $html[] = '</ul>';
        $html[] = '</div>';
        
        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' . '"></script>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function display_courses($courses)
    {
    	$setting = LocalSetting :: get('view_state', WeblcmsManager :: APPLICATION_NAME);
		
    	$category_0 = null;
	    $category_1 = null;
	    	
	    switch($setting)
	    {
	    	case self :: MIXED: 
	    			$category_0 = $this->category_factory($setting);
	    			break;
	    	case self :: SEPERATED:
	    			$arr = $this->category_factory($setting);
	    			$category_0 = $arr[0];
	    			$category_1 = $arr[1];
	    			break;
	    	case self :: OPEN_ONLY:
	    			$category_0 = $this->category_factory($setting);
	    			break;
	    }
        
	    $courses_category_0 = array();
	    $courses_category_1 = array();
	    
	    $wdm = WeblcmsDataManager::get_instance();
        while($course = $courses->next_result())
	    {
	    	
            $course = $wdm->retrieve_course($course->get_id());
            if($course)
            {
		    	switch($setting)
		    	{
			    	case self :: MIXED:
			    			$courses_category_0[] = $course;
			    			break;
			    	case self :: SEPERATED:
			    			if($course->get_access())
			    				$courses_category_0[] = $course;
			    			else
			    				$courses_category_1[] = $course;
			    			break;
			    	case self :: OPEN_ONLY:
			    			if($course->get_access())
			    				$courses_category_0[] = $course;
			    			break;
		    	}
            }
	    }
	    $html = array();
    	switch($setting)
    	{
	    	case self :: MIXED:
	    			$html[] = $this->display_course_digest($courses_category_0, $category_0);
	    			break;
	    	case self :: SEPERATED:
	    			$html[] = $this->display_course_digest($courses_category_0, $category_0);
	    			$html[] = $this->display_course_digest($courses_category_1, $category_1);
	    			break;
	    	case self :: OPEN_ONLY:
	    			$html[] = $this->display_course_digest($courses_category_0, $category_0);
	    			break;
	    }
	        
	    $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/home_ajax.js' . '"></script>';
	        
	    if ($_SESSION['toolbar_state'] == 'hide')
	    	$html[] = '<script type="text/javascript">var hide = "true";</script>';
	    else
	        $html[] = '<script type="text/javascript">var hide = "false";</script>';
	        
	    return implode("\n", $html);
    }  
    
    function display_create_course_link()
    {
        return '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_create.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE)) . '">' . Translation :: get('CourseCreate') . '</a></li>';
    }

    function display_edit_course_list_links()
    {
        $html = array();
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_reset.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT)) . '">' . Translation :: get('SortMyCourses') . '</a></li>';
        
        if(PlatformSetting :: get('show_subscribe_button_on_course_home', 'weblcms'))
        {
        	$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_subscribe.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_SUBSCRIBE)) . '">' . Translation :: get('CourseSubscribe') . '</a></li>';
        	$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_unsubscribe.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_UNSUBSCRIBE)) . '">' . Translation :: get('CourseUnsubscribe') . '</a></li>';
        }
        
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'treemenu_types/rss_feed.png)"><a style="top: -3px; position: relative;" href="' . RssIconGenerator :: generate_rss_url(WeblcmsManager :: APPLICATION_NAME, 'publication', $this->get_user()) . '">' . Translation :: get('RssFeed') . '</a></li>';
        return implode($html, "\n");
    }

    function display_platform_admin_course_list_links()
    {
        $html = array();
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_create.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE)) . '">' . Translation :: get('CourseCreate') . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER)) . '">' . Translation :: get('CourseList') . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER)) . '">' . Translation :: get('RequestList') . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_move.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_COURSE_CATEGORY_MANAGER)) . '">' . Translation :: get('CourseCategoryManagement') . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_add.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSES)) . '">' . Translation :: get('ImportCourseCSV') . '</a></li>';
        //$html[] = '<li><a href="'.$this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE)) .'">'.Translation :: get('AddUserToCourse').'</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_add.png)"><a style="top: -3px; position: relative;" href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSE_USERS)) . '">' . Translation :: get('ImportUsersForCourseCSV') . '</a></li>';
        
        return implode($html, "\n");
    }

    function display_course_digest($courses, $course_category = null)
    {
        $html = array();

        if(count($courses)>0)
        {
            $title = $course_category ? $course_category->get_title() : 'general';
            $html[] = '<div class="coursehomeblock block" id="courses_' . $title . '" style="background-image: url(' . Theme :: get_image_path('weblcms') . 'block_weblcms.png);">';
            $html[] = '<div class="title"><div style="float: left;">';
            
            if (isset($course_category))
            {
                $html[] = htmlentities($course_category->get_title());
            }
            else
            {
                $html[] = Translation :: get('GeneralCourses');
            }
            
            $html[] = '</div><a href="#" class="closeEl"><img class="visible" src="' . Theme :: get_common_image_path() . 'action_visible.png"/><img class="invisible" style="display: none;") src="' . Theme :: get_common_image_path() . 'action_invisible.png" /></a>';
            $html[] = '<div style="clear: both;"></div></div>';
            $html[] = '<div class="description">';
            $html[] = '<ul style="margin-left: -20px;">';
            foreach($courses as $course)
            {
                $wdm = WeblcmsDataManager::get_instance();
                $course = $wdm->retrieve_course($course->get_id());
                $tools = $course->get_tools();
                
                $html[] = '<li style="list-style: none; margin-bottom: 5px; list-style-image: url(' . Theme :: get_common_image_path() . 'action_home.png);"><a style="top: -2px; position: relative;" href="' . $this->get_course_viewing_url($course) . '">' . $course->get_name() . '</a>';

                foreach ($tools as $index => $tool)
                {
                    if ($tool->visible && $this->tool_has_new_publications($tool->name))
                    {
                        $params[WeblcmsManager :: PARAM_TOOL] = $tool->name;
                        $params[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
                        $params[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_VIEW_COURSE;
                        $url = $this->get_url($params);
                        $html[] = '<a href="' . $url . '"><img src="' . Theme :: get_image_path() . 'tool_' . $tool->name . '_new.png" alt="' . Translation :: get('New') . '"/></a>';
                    }
                }
                
                $text = array();
                
                if ($course->get_course_code_visible())
                {
                    $text[] = $course->get_visual();
                }
                
                if ($course->get_course_manager_name_visible())
                {
                    $user = UserDataManager :: get_instance()->retrieve_user($course->get_titular());
                    if($user)
                    {
                    	$text[] = $user->get_fullname();
                    }
                    else 
                    {
                    	$text[] = Translation :: get('NoTitular');
                    }
                }
                
                if ($course->get_course_languages_visible())
                {
                    $text[] = ucfirst($course->get_language());
                }
                
                if (count($text) > 0)
                {
                    $html[] = '<br />' . implode(' - ', $text);
                }
                
                $html[] = '</li>';
            }
            $html[] = '</ul>';
            
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '<br />';
        }
        
        return implode($html, "\n");
    }
    
    function category_factory($setting)
    {
    	switch($setting)	
    	{
    		case self :: MIXED :
			    	//Creating Mixed
					$cat = new CourseUserCategory();
					$cat->set_title(Translation :: get('Mixed'));
					$cat->set_user(0);
					$cat->set_sort(1);
					return $cat;
					break;
    		case self :: SEPERATED :
    				$arr = array();
					//creating Open
					$cat = new CourseUserCategory();
					$cat->set_title(Translation :: get('Open'));
					$cat->set_user(0);
					$cat->set_sort(2);
					$arr[] = $cat;			
					//creating Closed
					$cat = new CourseUserCategory();
					$cat->set_title(Translation :: get('Closed'));
					$cat->set_user(0);
					$cat->set_sort(3);
					$arr[] = $cat;
					return $arr;
					break;
    		case self :: OPEN_ONLY:
					//creating OpenOnly
					$cat = new CourseUserCategory();
					$cat->set_title(Translation :: get('OpenOnly'));
					$cat->set_user(0);
					$cat->set_sort(4);
					return $cat;
					break;
    	}	
    }
}
?>