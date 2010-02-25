<?php
class HtmlEditorAnnouncementRepoViewer extends HtmlEditorRepoViewer
{
    function HtmlEditorAnnouncementRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true, $redirect = true)
    {
        parent :: __construct($parent, $types, $mail_option, $maximum_select, $excluded_objects, $parse_input, $redirect);
        $this->set_types('announcement');
    }
}
?>