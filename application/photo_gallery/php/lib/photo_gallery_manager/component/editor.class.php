<?php
/**
 * $Id: editor.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.photo_gallery.photo_gallery_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('photo_gallery') . 'forms/photo_gallery_publication_form.class.php';

/**
 * Component to edit an existing photo_gallery_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class PhotoGalleryManagerEditorComponent extends PhotoGalleryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication = Request :: get(PhotoGalleryManager :: PARAM_PHOTO_GALLERY_ID);
        
        if (isset($publication))
        {
            $photo_gallery_publication = $this->retrieve_photo_gallery_publication($publication);
            
            //if (! $photo_gallery_publication->is_visible_for_target_user($this->get_user()))
            if (! $photo_gallery_publication->is_target($this->get_user()))
            {
                $this->not_allowed(null, false);
            }
            
            $content_object = $photo_gallery_publication->get_publication_object();
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => PhotoGalleryManager :: ACTION_EDIT, PhotoGalleryManager :: PARAM_PHOTO_GALLERY_ID => $publication)));
            
            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $form->update_content_object();
                }
                
                if ($form->is_version())
                {
                    $photo_gallery_publication->set_content_object($content_object->get_latest_version());
                    $photo_gallery_publication->update();
                }
                
                $publication_form = new PhotoGalleryPublicationForm(PhotoGalleryPublicationForm :: TYPE_SINGLE, $content_object, $this->get_user(), $this->get_url(array(PhotoGalleryManager :: PARAM_PHOTO_GALLERY_ID => $publication, 'validated' => 1)));
                $publication_form->set_publication($photo_gallery_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_content_object_publication();
                    $message = ($success ? 'ContentObjectUpdated' : 'ContentObjectNotUpdated');
                    
                    $this->redirect(Translation :: get($message), ! $success, array(Application :: PARAM_ACTION => PhotoGalleryManager :: ACTION_BROWSE), array(PhotoGalleryManager :: PARAM_PHOTO_GALLERY_ID));
                }
                else
                {
                    $this->display_header();
                    $publication_form->display();
                    $this->display_footer();
                }
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(PhotoGalleryManager :: PARAM_ACTION => PhotoGalleryManager :: ACTION_BROWSE));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('photo_gallery_updater');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PhotoGalleryManager :: PARAM_ACTION => PhotoGalleryManager :: ACTION_BROWSE)), Translation :: get('PhotoGalleryManagerBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PHOTO_GALLERY_ID);
    }

}
?>