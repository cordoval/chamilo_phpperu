<?php
//require_once dirname(__FILE__) . '/component/content_object_table/content_object_table.class.php';
require_once dirname(__FILE__) . '/html_editor_audio_repo_viewer_component.class.php';

class HtmlEditorAudioRepoViewer extends HtmlEditorRepoViewer
{
    function HtmlEditorAudioRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true, $redirect = true)
    {
        parent :: __construct($parent, $types, $mail_option, $maximum_select, $excluded_objects, $parse_input, $redirect);
        $this->set_types('document');
    }

    function get_repo_viewer_component($action)
    {
        return HtmlEditorAudioRepoViewerComponent :: factory($action, $this);
    }
}
?>