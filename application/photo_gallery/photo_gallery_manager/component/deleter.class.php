<?php
/**
 * $Id: deleter.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.photo_gallery.photo_gallery_manager.component
 */

/**
 * Component to delete photo_gallery_publications objects
 * @author Sven Vanpoucke
 * @author 
 */
class PhotoGalleryManagerDeleterComponent extends PhotoGalleryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(PhotoGalleryManager :: PARAM_PHOTO_GALLERY_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $photo_gallery_publication = $this->retrieve_photo_gallery_publication($id);
                if (! $photo_gallery_publication->is_target($this->get_user()))
                {
                    $failures ++;
                }
                else
                {
	                if (! $photo_gallery_publication->delete())
                    {
                        $failures ++;
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPhotoGalleryPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedPhotoGalleryPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPhotoGalleryPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedPhotoGalleryPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(PhotoGalleryManager :: PARAM_ACTION => PhotoGalleryManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPhotoGalleryPublicationsSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('photo_gallery_deleter');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PhotoGalleryManager :: PARAM_ACTION => PhotoGalleryManager :: ACTION_BROWSE)), Translation :: get('PhotoGalleryManagerBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PHOTO_GALLERY_ID);
    }
}
?>