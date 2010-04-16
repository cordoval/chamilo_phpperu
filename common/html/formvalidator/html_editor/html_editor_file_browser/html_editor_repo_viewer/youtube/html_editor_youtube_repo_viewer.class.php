<?php
class HtmlEditorYoutubeRepoViewer extends HtmlEditorRepoViewer
{
    function HtmlEditorYoutubeRepoViewer($parent, $types, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true)
    {
        parent :: __construct($parent, $types, $maximum_select, $excluded_objects, $parse_input);
        $this->set_types('youtube');
    }
}
?>