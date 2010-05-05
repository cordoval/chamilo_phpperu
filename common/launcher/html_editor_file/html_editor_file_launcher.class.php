<?php
require_once PATH :: get_library_path() . 'html/formvalidator/html_editor/html_editor_file_browser/html_editor_repo_viewer/html_editor_repo_viewer.class.php';
require_once PATH :: get_library_path() . 'html/formvalidator/html_editor/html_editor_file_browser/html_editor_processor/html_editor_processor.class.php';

class HtmlEditorFileLauncher extends LauncherApplication
{
    const PARAM_PLUGIN = 'plugin';
	const APPLICATION_NAME = 'html_editor_file';
    function HtmlEditorFileLauncher($user)
    {
        parent :: __construct($user);
        $this->set_parameter('CKEditor', Request :: get('CKEditor'));
        $this->set_parameter('CKEditorFuncNum', Request :: get('CKEditorFuncNum'));
        $this->set_parameter('langCode', Request :: get('langCode'));
    }

    function run()
    {
      $plugin = $this->get_plugin();
      $this->set_parameter(self :: PARAM_PLUGIN, $plugin);

      $repo_viewer = HtmlEditorRepoViewer :: factory($plugin, $this, array(), RepoViewer :: SELECT_SINGLE);

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
      }
    }
    
    function get_plugin()
    {
        return Request :: get(self :: PARAM_PLUGIN);
    }
    
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }
}
?>