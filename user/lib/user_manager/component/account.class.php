<?php
/**
 * $Id: account.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerAccountComponent extends UserManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('my_account');

        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyAccount')));
        $trail->add_help('user general');

        $user = $this->get_user();

        $form = new AccountForm(AccountForm :: TYPE_EDIT, $user, $this->get_url());

        if ($form->validate())
        {
            $success = $form->update_account();
            $this->redirect(Translation :: get($success ? 'UserProfileUpdated' : 'UserProfileNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_ACCOUNT));
        }
        else
        {
            $this->display_header($trail);

            $actions = array();
            $actions[] = 'account';

            if (PlatformSetting :: get('allow_buddy_management', 'user'))
            {
                //$actions[] = 'buddy_view';
            }
            
            $form_builder = new DynamicFormManager($this, UserManager :: APPLICATION_NAME, 'account_fields', DynamicFormManager :: TYPE_EXECUTER);
            $dynamic_form = $form_builder->get_form();
            if(count($dynamic_form->get_elements() > 0))
            {
            	$actions[] = 'account_extra';
            }

            if (count($actions) > 1)
            {
                echo '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
                foreach ($actions as $action)
                {
                    echo '<li><a';
                    if ($action == 'account')
                    {
                        echo ' class="current"';
                    }
                    echo ' href="' . $this->get_url(array(UserManager :: PARAM_ACTION => $action)) . '">' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($action) . 'Title')) . '</a></li>';
                }
                echo '</ul><div class="tabbed-pane-content"><br />';
            }

            $form->display();

            if (count($actions) > 1)
            {
                echo '</div></div>';
            }

            $this->display_footer();
        }
    }
}
?>