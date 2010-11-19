<?php
namespace user;

use common\libraries\Path;
use common\libraries\Application;
use common\libraries\CoreApplication;
use common\libraries\Redirect;
use common\libraries\Authentication;
use common\libraries\Translation;
use common\libraries\FormValidator;
use common\libraries\PlatformSetting;
use common\libraries\Request;
use common\libraries\Utilities;

/**
 * $Id: login.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.block
 */
require_once CoreApplication :: get_application_class_path('user') . 'blocks/user_block.class.php';

class UserLogin extends UserBlock
{

    function as_html()
    {
        $html = array();

        $html[] = $this->display_header();
        $html[] = $this->display_anonymous_right_menu();
        $html[] = $this->display_footer();

        return implode("\n", $html);
    }

    function display_anonymous_right_menu()
    {
        $html = array();

        if (! Authentication :: is_valid())
        {
            // TODO: New languageform
            //api_display_language_form();
            $html[] = $this->display_login_form();

            if (Request :: get('loginFailed') == 1)
            {
                $html[] = $this->handle_login_failed();
            }
        }
        else
        {
            $user = $this->get_user();

            $html[] = '<br />';
            $html[] = '<img src="' . $user->get_full_picture_url() . '" style="max-width: 100%;" />';
            $html[] = '<br /><br />';
            $html[] = $user->get_fullname() . '<br />';
            $html[] = $user->get_email() . '<br />';
            $html[] = '<br /><br />';
            $html[] = '<a href="' . Path :: get(WEB_PATH) . 'index.php?logout=true" class="button normal_button logout_button">' . Translation :: get('Logout') . '</a>';
            $html[] = '<br /><br />';

        //            if(PlatformSetting :: get('page_after_login') == 'weblcms')
        //			{
        //				//header('Location: run.php?application=weblcms');
        //				header('Location: index_repository_manager.php');
        //			}
        }

        return implode("\n", $html);

    }

    function handle_login_failed()
    {
        $message = Request :: get('message');
        return '<div class="error-message" style="width: auto; left: 0%; margin: auto; margin-left: -40px;">' . $message . '</div>';
        //return '<div style="background-color:#FFD1D1; background-image:url("../images/common/status_error_mini.png"); border:1px solid #FF9B9D; color:#B40404;">' . $message . '</div>';
    }

    function display_login_form()
    {
        $form = new FormValidator('formLogin');
        $renderer = & $form->defaultRenderer();
        $renderer->setElementTemplate('<div class="row">{label}<br />{element}</div>');
        $form->setRequiredNote(null);
        $form->addElement('text', 'login', Translation :: get('UserName'), array('style' => 'width: 90%;', 'onclick' => 'this.value=\'\';'));
        $form->addRule('login', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $form->addElement('password', 'password', Translation :: get('Password'), array('style' => 'width: 90%;', 'onclick' => 'this.value=\'\';'));
        $form->addRule('password', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');

        $buttons = array();
        $buttons[] = $form->createElement('style_submit_button', 'submitAuth', Translation :: get('Login'), array('class' => 'positive login'));

        if (PlatformSetting :: get('allow_registration', 'user') || PlatformSetting :: get('allow_password_retrieval', 'user'))
        {
            if (PlatformSetting :: get('allow_registration', 'user'))
            {
                $link = Redirect :: get_link(UserManager :: APPLICATION_NAME, array(Application :: PARAM_ACTION => UserManager :: ACTION_REGISTER_USER), array(), false, Redirect :: TYPE_CORE);
                $buttons[] = $form->createElement('static', null, null, '<a href="' . $link . '" class="button normal_button register_button">' . Translation :: get('Reg') . '</a>');
            }
            if (PlatformSetting :: get('allow_password_retrieval', 'user'))
            {
                $link = Redirect :: get_link(UserManager :: APPLICATION_NAME, array(Application :: PARAM_ACTION => UserManager :: ACTION_RESET_PASSWORD), array(), false, Redirect :: TYPE_CORE);
                $buttons[] = $form->createElement('static', null, null, '<a href="' . $link . '" class="button normal_button help_button">' . Translation :: get('ResetPassword') . '</a>');
            }
        }

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        //        $form->setDefaults(array('login' => Translation :: get('EnterUsername'), 'password' => '*******'));
        return $form->toHtml();
    }

    function is_editable()
    {
        return false;
    }

    function is_hidable()
    {
        return false;
    }

    function is_deletable()
    {
        return false;
    }
}
?>