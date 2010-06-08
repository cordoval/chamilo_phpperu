<?php
class StreamingMediaLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'streaming_media';
    
	function StreamingMediaLauncher($user)
    {
        parent :: __construct($user);
    }

    function run()
    {
      $type = $this->get_type();
      $this->set_parameter(StreamingMediaManager :: PARAM_TYPE, $type);

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
       
    function get_type()
    {
        return Request :: get(StreamingMediaManager :: PARAM_TYPE);
    }

	public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_APPLICATION)
    {
        // Use this untill PHP 5.3 is available
        // Then use get_class($this) :: APPLICATION_NAME
        // and remove the get_application_name function();
        //$application = $this->get_application_name();
        //return Redirect :: get_link($application, $parameters, $filter, $encode_entities, $application_type);
    }
    
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }
}
?>