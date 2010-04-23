<?php
class StreamingMediaLauncher
{
    const PARAM_TYPE = 'type';

    private $user;

    private $parameters;

    function StreamingMediaLauncher($user)
    {
        $this->set_user($user);
    }

    function run()
    {
      $type = $this->get_type();
      $this->set_parameter(self :: PARAM_TYPE, $type);

      $streaming_media_manager = StreamingMediaManager :: factory($type, $this);

      if (!$streaming_media_manager->is_ready_to_be_used())
      {
          $streaming_media_manager->run();
      }
      else
      {
          //$processor = HtmlEditorProcessor :: factory($plugin, $this, $repo_viewer->get_selected_objects());

          $this->display_header();
          //$processor->run();
          echo('in else of run');
          $this->display_footer();

          // Go to real processing depending on selected editor.
//          echo "<script type='text/javascript'>window.opener.CKEDITOR.tools.callFunction(" . $this->get_parameter('CKEditorFuncNum') . ", 'image.jpg', 'Message !');</script>";
      }
    }
    
    function create_component($type, $application = null)
    {
		if($application == null)
    	{
    		$application = $this;
    	}
    	
        $manager_class = get_class($application);
        $application_component_path = $application->get_application_component_path();
        		
        $file = $application_component_path . Utilities :: camelcase_to_underscores($type) . '.class.php';

        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('ComponentFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get($manager_class) . '</li>';
            $message[] = '<li>' . Translation :: get($type) . '</li>';
            $message[] = '</ul>';

            $application_name = Application :: application_to_class($this->get_application_name());

            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb('#', Translation :: get($application_name)));

            Display :: header($trail);
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }

        $class = $manager_class . $type . 'Component';
        require_once $file;

        if(is_subclass_of($application, 'SubManager'))
        {
        	$component = new $class($application->get_parent());
        }
        else
        {
	        $component = new $class($this->get_user());
	        $component->set_parameters($this->get_parameters());
        }
        return $component;
    }
    
    public function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $parameters = (count($parameters) ? array_merge($this->get_parameters(), $parameters) : $this->get_parameters());
        return Redirect :: get_url($parameters, $filter, $encode_entities);
    }
    

    function display_header()
    {
    	Display :: small_header();
    }
    
    function display_footer()
    {
    	Display :: small_footer();
    }
    
    function get_type()
    {
        return Request :: get(self :: PARAM_TYPE);
    }

    function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }

    function get_parameters()
    {
        return $this->parameters;
    }

    function get_parameter($key)
    {
        return $this->parameters[$key];
    }

    function set_parameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    function set_user($user)
    {
        $this->user = $user;
    }

    function get_user()
    {
        return $this->user;
    }

    function get_user_id()
    {
        return $this->get_user()->get_id();
    }
    
	public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_APPLICATION)
    {
        // Use this untill PHP 5.3 is available
        // Then use get_class($this) :: APPLICATION_NAME
        // and remove the get_application_name function();
        //$application = $this->get_application_name();
        //return Redirect :: get_link($application, $parameters, $filter, $encode_entities, $application_type);
    }
}
?>