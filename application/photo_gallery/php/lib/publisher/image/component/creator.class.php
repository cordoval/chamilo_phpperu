<?php
namespace application\photo_gallery;

use common\libraries\Path;
use common\extensions\repo_viewer\RepoViewerCreatorComponent;
use repository\content_object\document\Document;
  
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to create a new learning object before publishing it.
 */
class PhotoGalleryRepoViewerCreatorComponent extends RepoViewerCreatorComponent
{
    function get_object_form_variant()
    {
        return Document :: TYPE_IMAGE;
    }
}
?>