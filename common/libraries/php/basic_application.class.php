<?php
abstract class BasicApplication extends Application
{

    abstract function is_active($application);

    abstract function get_application_component_path();

    public static function get_selecter($url, $current_application = null)
    {
        $html = array();
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/application.js');
        $html[] = '<div class="application_selecter">';
        
        $the_applications = WebApplication :: load_all();
        $the_applications = array_merge(CoreApplication :: get_list(), $the_applications);
        
        foreach ($the_applications as $the_application)
        {
            if (isset($current_application) && $current_application == $the_application)
            {
                $type = 'application current';
            }
            else
            {
                $type = 'application';
            }
            
            $application_name = Translation :: get(Utilities :: underscores_to_camelcase($the_application));
            
            $html[] = '<a href="' . str_replace(self :: PLACEHOLDER_APPLICATION, $the_application, $url) . '">';
            $html[] = '<div class="' . $type . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_' . $the_application . '.png);">' . $application_name . '</div>';
            $html[] = '</a>';
        }
        
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        
        return implode("\n", $html);
    }

    static function get_application_class_name($application)
    {
        return Application :: application_to_class($application) . 'Manager';
    }

    static function is_application($application)
    {
        if (WebApplication :: is_application($application) || CoreApplication :: is_application($application))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static function get_application_web_path($application_name)
    {
        if (WebApplication :: is_application($application_name))
        {
            return WebApplication :: get_application_web_path($application_name);
        }
        else
        {
            return CoreApplication :: get_application_web_path($application_name);
        }
    }

    static function get_application_path($application_name)
    {
        if (WebApplication :: is_application($application_name))
        {
            return WebApplication :: get_application_class_path($application_name);
        }
        else
        {
            return CoreApplication :: get_application_class_path($application_name);
        }
    }

    static function factory($application, $user)
    {
        if (WebApplication :: is_application($application))
        {
            return WebApplication :: factory($application, $user);
        }
        else
        {
            return CoreApplication :: factory($application, $user);
        }
    }

    /**
     * Gets a link to the personal calendar application
     * @param array $parameters
     * @param boolean $encode
     */
    public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_APPLICATION)
    {
        // Use this untill PHP 5.3 is available
        // Then use get_class($this) :: APPLICATION_NAME
        // and remove the get_application_name function();
        $application = $this->get_application_name();
        return Redirect :: get_link($application, $parameters, $filter, $encode_entities, $application_type);
    }
 
    static function get_application_manager_path($application_name)
    {
   		$type = Application::get_type($application_name);
    	return $type :: get_application_class_path($application_name) . 'lib/' . $application_name . '_manager' . '/' . $application_name . '_manager.class.php';
    } 
    
    static function get_component_path($application)
    {
        $type = Application::get_type($application);
    	return $type :: get_application_class_path($application) . 'lib/' . $application . '_manager/component/';
    }

    static function get_application_class_path($application)
    {
    	$type = Application::get_type($application);
    	return $type :: get_application_path($application) . Path :: CLASS_PATH . '/';
    }
    
    static function get_application_class_lib_path($application)
    {
    	return self ::get_application_class_path($application) . Path :: CLASS_LIB_PATH . '/'; 
    }
    
 	static function get_application_resources_path($application)
    {
    	$type = Application::get_type($application);
    	return $type :: get_application_path($application) . Path :: RESOURCES_PATH . '/';
    }
    
 	static function get_application_web_resources_path($application)
    {
    	$type = Application::get_type($application);
    	return $type :: get_application_web_path($application) . Path :: RESOURCES_PATH . '/';
    }

 	static function get_application_resources_images_path($application)
    {
    	return self :: get_application_resources_path($application) . Path :: RESOURCES_IMAGES_PATH . '/';
    }
 	
    static function get_application_resources_i18n_path($application)
    {
    	return self :: get_application_resources_path($application) . Path :: RESOURCES_I18N_PATH . '/';
    }
 	
    static function get_application_resources_css_path($application)
    {
    	return self :: get_application_resources_path($application) . Path :: RESOURCES_CSS_PATH . '/';
    }   
    
 	static function get_application_resources_templates_path($application)
    {
    	return self :: get_application_resources_path($application) . Path :: RESOURCES_TEMPLATES_PATH . '/';
    }
    
 	static function get_application_resources_javascript_path($application)
    {
    	return self :: get_application_resources_path($application) . Path :: RESOURCES_JAVASCRIPT_PATH . '/';
    }
    //web_path
	static function get_application_web_resources_images_path($application)
    {
    	return self :: get_application_web_resources_path($application) . Path :: RESOURCES_IMAGES_PATH . '/';
    }
 	
    static function get_application_web_resources_i18n_path($application)
    {
    	return self :: get_application_web_resources_path($application) . Path :: RESOURCES_I18N_PATH . '/';
    }
 	
    static function get_application_web_resources_css_path($application)
    {
    	return self :: get_application_web_resources_path($application) . Path :: RESOURCES_CSS_PATH . '/';
    }   
    
 	static function get_application_web_resources_templates_path($application)
    {
    	return self :: get_application_web_resources_path($application) . Path :: RESOURCES_TEMPLATES_PATH . '/';
    }
    
 	static function get_application_web_resources_javascript_path($application)
    {
    	return self :: get_application_web_resources_path($application) . Path :: RESOURCES_JAVASCRIPT_PATH . '/';
    }
    
    static function exists($application)
    {
        if (WebApplication :: exists($application))
        {
        	return WebApplication :: CLASS_NAME;
        }
        elseif (CoreApplication :: exists($application))
        {
            return CoreApplication :: CLASS_NAME;
        }
        else
        {
            return false;
        }
    }
}
?>