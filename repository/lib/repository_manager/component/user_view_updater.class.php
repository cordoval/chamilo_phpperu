<?php
/**
 * $Id: user_view_updater.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

class RepositoryManagerUserViewUpdaterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        
        $id = Request :: get(RepositoryManager :: PARAM_USER_VIEW);
        if ($id)
        {
            $user_view = $this->retrieve_user_views(new EqualityCondition(UserView :: PROPERTY_ID, $id))->next_result();
            
            /*if (! $this->get_user()->is_platform_admin())
            {
                $this->display_header($trail, false, true);
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }*/
            
            $form = new UserViewForm(UserViewForm :: TYPE_EDIT, $user_view, $this->get_url(array(RepositoryManager :: PARAM_USER_VIEW => $id)), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_user_view();
                $user_view = $form->get_user_view();
                
                $message = Translation :: get($success ? 'UserViewUpdated' : 'UserViewNotUpdated');
                
                if(!$success)
                {
                	$message .= '<br />' . implode('<br /', $user_view->get_errors());
                }
                
                $this->redirect($message, ($success ? false : true), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS));
            }
            else
            {
                $this->display_header($trail, false, true);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoUserViewSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS)), Translation :: get('RepositoryManagerUserViewBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_user_view_updater');
    }
    
	function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_USER_VIEW);
    }
}
?>