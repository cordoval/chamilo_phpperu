<?php
/**
 * $Id: repo_viewer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.repository_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class RepositoryManagerRepoViewerComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $repo_viewer = new RepoViewer($this, Document :: get_type_name(), RepoViewer :: SELECT_SINGLE, array(), true);
        if (!$repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $html[] = '<script type="text/javascript">';
            $html[] = 'window.parent.object_selected(' . $repo_viewer->get_selected_objects() . ');';
            $html[] = '</script>';
        } 
    }
}
?>