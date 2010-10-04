<?php
/**
 * $Id: editor.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerEditorComponent extends HomeManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $id = Request :: get(HomeManager :: PARAM_HOME_ID);
        $type = Request :: get(HomeManager :: PARAM_HOME_TYPE);
        
        $user = $this->get_user();
        $user_home_allowed = $this->get_platform_setting('allow_user_home');
        
        // TODO: Introduce an extra parameter to allow admins to adapt a user's homepage
        

        if ($user_home_allowed && Authentication :: is_valid())
        {
            $user_id = $user->get_id();
        }
        else
        {
            if (! $user->is_platform_admin())
            {
                $this->display_header();
                Display :: error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $user_id = '0';
        }
        
        if ($id && $type)
        {
            $url = $this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_EDIT_HOME, HomeManager :: PARAM_HOME_TYPE => $type, HomeManager :: PARAM_HOME_ID => $id));
            switch ($type)
            {
                case HomeManager :: TYPE_BLOCK :
                    $object = $this->retrieve_home_block($id);
                    $form = new HomeBlockForm(HomeBlockForm :: TYPE_EDIT, $object, $url);
                    break;
                case HomeManager :: TYPE_COLUMN :
                    $object = $this->retrieve_home_column($id);
                    $form = new HomeColumnForm(HomeColumnForm :: TYPE_EDIT, $object, $url);
                    break;
                case HomeManager :: TYPE_ROW :
                    $object = $this->retrieve_home_row($id);
                    $form = new HomeRowForm(HomeRowForm :: TYPE_EDIT, $object, $url);
                    break;
                case HomeManager :: TYPE_TAB :
                    $object = $this->retrieve_home_tab($id);
                    $form = new HomeTabForm(HomeTabForm :: TYPE_EDIT, $object, $url);
                    break;
            }
            
            if ($object->get_user() == $user_id || ($object->get_user() == '0' && $user->is_platform_admin()))
            {
                if ($form->validate())
                {
                    $success = $form->update_object();
                    $this->redirect(Translation :: get($success ? 'HomeUpdated' : 'HomeNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => HomeManager :: ACTION_VIEW_HOME));
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
                $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('HomeManagerManagerComponent')));
    	$breadcrumbtrail->add_help('home_editor');
    }
    
    function get_additional_parameters()
    {
    	return array(HomeManager :: PARAM_HOME_TYPE, HomeManager :: PARAM_HOME_ID);
    }
}
?>