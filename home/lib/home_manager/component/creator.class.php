<?php
/**
 * $Id: creator.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerCreatorComponent extends HomeManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $type = Request :: get(HomeManager :: PARAM_HOME_TYPE);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => HomeManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Home')));
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('HomeManager')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeCreator')));
        $trail->add_help('home general');
        
        $user = $this->get_user();
        $user_home_allowed = $this->get_platform_setting('allow_user_home');
        
        if ($user_home_allowed && Authentication :: is_valid())
        {
            $user_id = $user->get_id();
        }
        else
        {
            if (! $user->is_platform_admin())
            {
                $this->display_header($trail);
                Display :: error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $user_id = '0';
        }
        
        if ($type)
        {
            $url = $this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_CREATE_HOME, HomeManager :: PARAM_HOME_TYPE => $type));
            switch ($type)
            {
                case HomeManager :: TYPE_BLOCK :
                    $object = new HomeBlock();
                    $object->set_user($user_id);
                    $form = new HomeBlockForm(HomeBlockForm :: TYPE_CREATE, $object, $url);
                    break;
                case HomeManager :: TYPE_COLUMN :
                    $object = new HomeColumn();
                    $object->set_user($user_id);
                    $form = new HomeColumnForm(HomeColumnForm :: TYPE_CREATE, $object, $url);
                    break;
                case HomeManager :: TYPE_ROW :
                    $object = new HomeRow();
                    $object->set_user($user_id);
                    $form = new HomeRowForm(HomeRowForm :: TYPE_CREATE, $object, $url);
                    break;
                case HomeManager :: TYPE_TAB :
                    $object = new HomeTab();
                    $object->set_user($user_id);
                    $form = new HomeTabForm(HomeTabForm :: TYPE_CREATE, $object, $url);
                    break;
            }
            
            if ($object->get_user() == $user_id || ($object->get_user() == '0' && $user->is_platform_admin()))
            {
                if ($form->validate())
                {
                    $success = $form->create_object();
                    $this->redirect(Translation :: get($success ? 'HomeCreated' : 'HomeNotCreated'), ($success ? false : true), array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
                }
                else
                {
                    $this->display_header($trail);
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
}
?>