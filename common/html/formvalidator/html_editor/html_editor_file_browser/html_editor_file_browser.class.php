<?php
require_once dirname(__FILE__) . '/html_editor_repo_viewer/html_editor_repo_viewer.class.php';
require_once dirname(__FILE__) . '/html_editor_processor/html_editor_processor.class.php';

class HtmlEditorFileBrowser
{
    const PARAM_PLUGIN = 'plugin';

    private $user;

    private $parameters;

    function HtmlEditorFileBrowser($user)
    {
        $this->set_user($user);
    }

    function run()
    {
      $plugin = $this->get_plugin();
      $this->set_parameter(self :: PARAM_PLUGIN, $plugin);

      $repo_viewer = HtmlEditorRepoViewer :: factory($plugin, $this, array(), false, RepoViewer :: SELECT_SINGLE);

      if (!$repo_viewer->is_ready_to_be_published())
      {
          $html = $repo_viewer->as_html();
          $this->display_header();
          echo $html;
          $this->display_footer();
      }
      else
      {
          $processor = HtmlEditorProcessor :: factory($plugin, $this, $repo_viewer->get_selected_objects());

          $this->display_header();
          $processor->run();
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
    
    function get_plugin()
    {
        return Request :: get(self :: PARAM_PLUGIN);
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