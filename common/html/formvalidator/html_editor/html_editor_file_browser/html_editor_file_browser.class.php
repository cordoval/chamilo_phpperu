<?php
require_once dirname(__FILE__) . '/html_editor_repo_viewer/html_editor_repo_viewer.class.php';

class HtmlEditorFileBrowser
{
    private $content_object_types;

    private $user;

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
        $this->content_object_types = $content_object_types;
    }

    function run()
    {
      $object = Request :: get('object');
      $repo = new HtmlEditorRepoViewer($this, 'announcement');

      if (! isset($object))
      {
          echo $repo->as_html();
      }
      else
      {
          // Go to real processing depending on selected editor.
          echo 'Selection made';
      }
    }

    function get_parameters()
    {
        return array('test' => 'one');
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