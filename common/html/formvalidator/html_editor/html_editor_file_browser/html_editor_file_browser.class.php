<?php
require_once dirname(__FILE__) . '/html_editor_repo_viewer/html_editor_repo_viewer.class.php';

class HtmlEditorFileBrowser
{
    const PARAM_TYPE = 'type';

    private $user;

    private $parameters;

    function HtmlEditorFileBrowser($user)
    {
        $this->set_user($user);
    }

    function run()
    {
      $type = $this->get_repo_viewer_type();
      $this->set_parameter(self :: PARAM_TYPE, $type);

      $repo_viewer = HtmlEditorRepoViewer :: factory($type, $this, array(), false, RepoViewer :: SELECT_SINGLE);

      if (!$repo_viewer->is_ready_to_be_published())
      {
          echo $repo_viewer->as_html();
      }
      else
      {
          // Go to real processing depending on selected editor.
          echo "<script type='text/javascript'>window.opener.CKEDITOR.tools.callFunction(" . $this->get_parameter('CKEditorFuncNum') . ", 'image.jpg', 'Message !');</script>";
      }
    }

    function get_repo_viewer_type()
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