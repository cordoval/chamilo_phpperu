<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../photo_gallery_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/photo_gallery_publisher.class.php';
require_once dirname(__FILE__) . '/../../publisher/image/photo_gallery_repo_viewer.class.php';
//require_once dirname(__FILE__) . '/../../photo_gallery_rights.class.php';

class PhotoGalleryManagerPublisherComponent extends PhotoGalleryManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//        if(! PhotoGalleryRights :: is_allowed_in_personal_calendar_subtree(PhotoGalleryRights :: RIGHT_PUBLISH, PhotoGalleryRights :: get_personal_calendar_subtree_root()))
//        {
//            $this->display_header();
//            Display :: error_message(Translation :: get("NotAllowed"));
//            $this->display_footer();
//            exit();
//        }
        if (!PhotoGalleryRepoViewer :: is_ready_to_be_published())
        {
        	$repo_viewer = PhotoGalleryRepoViewer::construct($this);
        	$repo_viewer->run();
        }
        else
        {
            $publisher = new PhotoGalleryPublisher($this);
            $publisher->get_publications_form(PhotoGalleryRepoViewer :: get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name());
    }
}
?>