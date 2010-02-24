<?php
class HtmlEditorFileBrowser
{
    private $content_object_types;

    private $user;
    
    private $parameters;

    public static function factory($type, $user)
    {
        $file = dirname(__FILE__) . '/' . $type . '_file_browser/' . $type . '_file_browser.class.php';
        $class = Utilities :: underscores_to_camelcase($type) . 'HtmlEditorFileBrowser';

        if (file_exists($file))
        {
            require_once ($file);
            return new $class($user);
        }
    }

    function HtmlEditorFileBrowser($user)
    {
        $this->set_user($user);
    }

    function get_content_object_types()
    {
        return $this->content_object_types;
    }

    function set_content_object_types($content_object_types)
    {
        if (!is_array($content_object_types))
        {
            $content_object_types = array($content_object_types);
        }
        
        $this->content_object_types = $content_object_types;
    }

    function run()
    {
      $object = Request :: get('object');
      $repo_viewer = new RepoViewer($this, $this->get_content_object_types(), false, RepoViewer :: SELECT_SINGLE);

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