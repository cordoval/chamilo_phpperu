<?php
require_once dirname(__FILE__) . '/competence_repo_viewer_component.class.php';

class CompetenceRepoViewer extends Repoviewer
{
    function CompetenceRepoViewer($parent, $types, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array())
    {
        parent :: __construct($parent, $types, $maximum_select, $excluded_objects, false);
    }
    
    function get_repo_viewer_component($action)
    {
        return CompetenceRepoViewerComponent :: factory($action, $this);
    }
}
?>