<?php
namespace user;

use common\libraries\Header;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\BreadcrumbTrail;
use common\libraries\PlatformSetting;
use common\libraries\DynamicVisualTabsRenderer;
use common\libraries\DynamicVisualTab;
use common\libraries\Translation;
use common\libraries\Application;

use common\extensions\dynamic_form_manager\DynamicFormManager;
/**
 * $Id: account.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerAccountComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('my_account');

        $user = $this->get_user();

        $form = new AccountForm(AccountForm :: TYPE_EDIT, $user, $this->get_url());

        if ($form->validate())
        {
            $success = $form->update_account();
            if(!$success)
            {
                if(isset($_FILES['picture_uri']) &&  $_FILES['picture_uri']['error'])
                {
                    $neg_message = 'FileTooBig';
        }
        else
        {
                    $neg_message = 'UserProfileNotUpdated';
                }
            }
            else
            {
                $neg_message = 'UserProfileNotUpdated';
                $pos_message = 'UserProfileUpdated';
            }
            $this->redirect(Translation :: get($success ? $pos_message : $neg_message), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_ACCOUNT));
        }
        else
        {
            $this->display_header();

            $actions = array();

            $actions[] = UserManager :: ACTION_VIEW_ACCOUNT;

            if (PlatformSetting :: get('allow_buddy_management', 'user'))
            {
                //$actions[] = 'buddy_view';
            }

            $actions[] = UserManager :: ACTION_USER_SETTINGS;

            $form_builder = new DynamicFormManager($this, UserManager :: APPLICATION_NAME, 'account_fields', DynamicFormManager :: TYPE_EXECUTER);
            $dynamic_form = $form_builder->get_form();
            if (count($dynamic_form->get_elements()) > 0)
            {
                $actions[] = UserManager :: ACTION_ADDITIONAL_ACCOUNT_INFORMATION;
            }

            if (count($actions) > 1)
            {
                $tabs = new DynamicVisualTabsRenderer('account', $form->toHtml());
                foreach ($actions as $action)
                {
                    $selected = ($action == 'account' ? true : false);

                    $label = htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($action) . 'Title'));
                    $link = $this->get_url(array(UserManager :: PARAM_ACTION => $action));

                    $tabs->add_tab(new DynamicVisualTab($action, $label, Theme :: get_image_path() . 'place_' . $action . '.png', $link, $selected));

                }
                echo $tabs->render();
            }
            else
            {
                $form->display();
            }

            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('user_account');
    }
}
?>