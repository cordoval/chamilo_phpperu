<?php
class HtmlEditorYoutubeRepoViewer extends HtmlEditorRepoViewer
{
    function HtmlEditorYoutubeRepoViewer($parent, $types, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true)
    {
        parent :: __construct($parent, $types, $maximum_select, $excluded_objects, $parse_input);
        $this->set_types(Youtube :: get_type_name());
    }
    
 	function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }
}
?>