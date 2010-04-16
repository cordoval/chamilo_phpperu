<?php
/**
 * $Id: repo_viewer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.repository_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class RepositoryManagerRepoViewerComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Display :: small_header();
        $pub = new RepoViewer($this, 'document', RepoViewer :: SELECT_SINGLE, array(), true);
        if (!$pub->is_ready_to_be_published())
        {
            echo $pub->as_html();
        }
        else
        {
            $html[] = '<script type="text/javascript">';
            $html[] = 'window.parent.object_selected(' . $pub->get_selected_objects() . ');';
            $html[] = '</script>';
            echo implode("\n", $html);
        }
    }
}
?>