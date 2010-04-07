<?php
require_once dirname(__FILE__) . '/competence_repo_viewer_component.class.php';

class CompetenceRepoViewer extends Repoviewer
{
    function CompetenceRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array())
    {
        parent :: __construct($parent, $types, $mail_option, $maximum_select, $excluded_objects, false, false);
    }
    
    function get_repo_viewer_component($action)
    {
        return CompetenceRepoViewerComponent :: factory($action, $this);
    }
}
?>