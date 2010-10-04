<?php
/**
 * $Id: configurer.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerConfigurerComponent extends HomeManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(HomeManager :: PARAM_HOME_ID);
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if ($id)
        {
            $url = $this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_CONFIGURE_HOME, HomeManager :: PARAM_HOME_ID => $id));
            
            $object = $this->retrieve_home_block($id);
            
            if ($object->is_configurable())
            {
                $form = new HomeBlockConfigForm($object, $url);
                
                if ($form->validate())
                {
                    $success = $form->update_block_config();
                    //$this->redirect(Translation :: get($success ? 'BlockConfigUpdated' : 'BlockConfigNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
                    $message = ($success ? 'message=' : 'error_message=') . Translation :: get($success ? 'BlockConfigUpdated' : 'BlockConfigNotUpdated');
                    header('Location: index.php?' . $message);
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
                $this->display_header();
                $this->display_warning_message(Translation :: get('NothingToConfigure'));
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoHomeBlockSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('home_configurer');
    }
    
    function get_additional_parameters()
    {
    	return array(HomeManager :: PARAM_HOME_ID);
    }
}
?>