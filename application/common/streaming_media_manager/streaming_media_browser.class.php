<?php
class StreamingMediaBrowser
{
    const PARAM_TYPE = 'type';

    private $user;

    private $parameters;

    function StreamingMediaBrowser($user)
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
}
?>