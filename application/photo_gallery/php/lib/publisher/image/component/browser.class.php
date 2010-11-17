<?php
namespace application\photo_gallery;

use common\extensions\repo_viewer\RepoViewerBrowserComponent;
use common\libraries\Path;
use common\libraries\ImageContentObjectTable;
/**
 * $Id: browser.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
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