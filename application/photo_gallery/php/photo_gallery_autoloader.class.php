<?php
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class PhotoGalleryAutoloader
{
	static function load($classname)
	{
		$list = array(
        	'photo_gallery_publication' => 'photo_gallery_publication.class.php',
			'photo_gallery_data_manager_interface' => 'photo_gallery_data_manager_interface.class.php',
			'photo_gallery_publication_user' => 'photo_gallery_publication_user.class.php', 
			'photo_gallery_publication_group' => 'photo_gallery_publication_group.class.php',
			'photo_gallery_data_manager' => 'photo_gallery_data_manager.class.php', 
			'photo_gallery_publication_renderer' => 'photo_gallery_publication_renderer.class.php',
			'photo_gallery_manager' => 'photo_gallery_manager/photo_gallery_manager.class.php');       
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('photo_gallery') . $url;
            return true;
        }
        
        return false;
	}
}

?>