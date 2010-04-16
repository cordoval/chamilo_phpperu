<?php
//require_once dirname(__FILE__) . '/component/content_object_table/content_object_table.class.php';
require_once dirname(__FILE__) . '/html_editor_image_repo_viewer_component.class.php';

class HtmlEditorImageRepoViewer extends HtmlEditorRepoViewer
{
    function HtmlEditorImageRepoViewer($parent, $types, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true)
    {
        parent :: __construct($parent, $types, $maximum_select, $excluded_objects, $parse_input);
        $this->set_types('document');
    }
    
    function get_repo_viewer_component($action)
    {
        return HtmlEditorImageRepoViewerComponent :: factory($action, $this);
    }
}
?>