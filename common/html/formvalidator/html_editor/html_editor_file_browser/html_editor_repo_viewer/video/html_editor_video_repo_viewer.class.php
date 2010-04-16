<?php
//require_once dirname(__FILE__) . '/component/content_object_table/content_object_table.class.php';
require_once dirname(__FILE__) . '/html_editor_video_repo_viewer_component.class.php';

class HtmlEditorVideoRepoViewer extends HtmlEditorRepoViewer
{
    function HtmlEditorVideoRepoViewer($parent, $types, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true)
    {
        parent :: __construct($parent, $types, $maximum_select, $excluded_objects, $parse_input);
        $this->set_types('document');
    }

    function get_repo_viewer_component($action)
    {
        return HtmlEditorVideoRepoViewerComponent :: factory($action, $this);
    }
}
?>