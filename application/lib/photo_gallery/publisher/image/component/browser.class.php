<?php
/**
 * $Id: browser.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/browser.class.php';
require_once Path :: get_common_path() . 'html/formvalidator/html_editor/html_editor_file_browser/html_editor_repo_viewer/image/component/image_content_object_table/image_content_object_table.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class PhotoGalleryRepoViewerBrowserComponent extends RepoViewerBrowserComponent
{
    protected function get_object_table($actions)
    {
        return new ImageContentObjectTable($this, $this->get_user(), $this->get_types(), $this->get_query(), $actions);
    }
}
?>