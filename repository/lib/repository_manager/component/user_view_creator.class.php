<?php
/**
 * $Id: user_view_creator.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerUserViewCreatorComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        /*if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }*/
        $user_view = new UserView();
        $user_view->set_user_id($this->get_user_id());
        $form = new UserViewForm(UserViewForm :: TYPE_CREATE, $user_view, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_user_view();
            $user_view = $form->get_user_view();
            
            $message = $success ? Translation :: get('UserViewCreated') : Translation :: get('UserViewNotCreated');
            
            if(!$success)
            {
                $message .= '<br />' . implode('<br /', $user_view->get_errors());
            }
            
            $this->redirect($message, $success ? false : true, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS));
        }
        else
        {
            $this->display_header(null, false, true);
            $form->display();
            $this->display_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS)), Translation :: get('RepositoryManagerUserViewBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_user_view_creator');
    }
}
?>