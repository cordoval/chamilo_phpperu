<?php
require_once PATH :: get_library_path() . 'html/formvalidator/html_editor/html_editor_file_browser/html_editor_repo_viewer/html_editor_repo_viewer.class.php';
require_once PATH :: get_library_path() . 'html/formvalidator/html_editor/html_editor_file_browser/html_editor_processor/html_editor_processor.class.php';

class HtmlEditorFileLauncher extends LauncherApplication
{
    const PARAM_PLUGIN = 'plugin';
    const APPLICATION_NAME = 'html_editor_file';

    private $content_object_types;

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

        

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = HtmlEditorRepoViewer :: construct($plugin, $this);
            $repo_viewer->set_maximum_select(RepoViewer :: SELECT_SINGLE);
            $this->content_object_types = call_user_func(array(get_class($repo_viewer)));
            $repo_viewer->run();
        }
        else
        {
            $processor = HtmlEditorProcessor :: factory($plugin, $this, RepoViewer::get_selected_objects());

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

    function get_allowed_content_object_types()
    {
        $class = 'HtmlEditor' . Utilities :: underscores_to_camelcase($this->get_plugin()) . 'RepoViewer';
        return call_user_func(array($class, 'get_allowed_content_object_types'));
    }
}
?>