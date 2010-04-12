<?php
class ReportingViewerSaverComponent extends ReportingViewerComponent
{

    function run()
    {
        $template_registration = $this->get_template();
        
        $template = ReportingTemplate :: factory($template_registration, $this);
        $export_type = Request :: get(ReportingManager :: PARAM_EXPORT_TYPE);
        $export = ReportingExporter :: factory($export_type, $template);
        $file = $export->save();
        
        
        
//        $repo_path = Path :: get(SYS_REPO_PATH);
//        //
//        $user_id = UserManager::PARAM_USER_USER_ID;
//        
//        $owner_path = $repo_path . $user_id;
//        Filesystem :: create_dir($owner_path);
//        
//        //$filename = Translation :: get('LaikaResults') . '.html';
//        $filename = Utilities::underscores_to_camelcase($template) . '.html';
//        $filename = Filesystem :: create_unique_name($owner_path, $filename);
//        $path = $user_id . '/' . $filename;
//        $full_path = $repo_path . $path;
//        Filesystem :: write_to_file($full_path, strip_tags($display_html, '<table><tr><td><th><div><span><img>'));
//        
		require_once PATH::get_repository_path() . 'lib/content_object/document/document.class.php';
        $html_object = new Document();
        $html_object->set_title(Utilities::underscores_to_camelcase_with_spaces($template->get_name()));
        $html_object->set_description(Utilities::underscores_to_camelcase_with_spaces($template->get_name()));
        $html_object->set_parent_id(0);
        $html_object->set_owner_id($this->get_user_id());
        //$html_object->set_path($path);
        $html_object->set_filename($export->get_file_name() . '.' . $export_type);
        //$html_object->set_filesize(Filesystem :: get_disk_space($full_path));
        
        $html_object->set_in_memory_file($file);
        
        if (!$html_object->create())
        {
        	$message = 'ObjectNoCreated';
        	$error = true;
        }
        else
        {
        	$message = 'SavedToRepository';
        	$error = false;
        }
        $parameters = $template->get_parameters();
        $parameters[ReportingViewer::PARAM_REPORTING_VIEWER_ACTION] = ReportingViewer::ACTION_VIEW_TEMPLATE;
        $this->redirect(Translation::get($message), $error, $parameters);
        
        //$this->display_footer();
    }
}
?>