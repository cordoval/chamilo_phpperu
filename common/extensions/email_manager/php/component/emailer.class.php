<?php
namespace common\extensions\email_manager;

use user\User;
use common\libraries\BreadcrumbTrail;
use common\libraries\PlatformSetting;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;

/**
 * $Id: emailer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.email_manager.component
 */

require_once dirname(__FILE__) . '/../email_form.class.php';

class EmailManagerEmailerComponent extends EmailManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        if (PlatformSetting :: get('active_online_email_editor') == 0)
        {
            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $form = new EmailForm($this->get_url(), $this->get_user(), $this->get_target_users());

        if ($form->validate())
        {
            $success = $form->email();
            $this->redirect(Translation :: get($success ? 'EmailSent' : 'EmailNotSent'), ($success ? false : true), array());

        }
        else
        {
            $this->display_header($trail);

            echo $this->display_targets();

            $form->display();
            $this->display_footer();
        }
    }

    function display_targets()
    {
        $target_users = $this->get_target_users();
        $html = array();

        $html[] = '<div class="content_object padding_10">';
        $html[] = '<div class="title">' . Translation :: get('SelectedUsers') . '</div>';
        $html[] = '<div class="description">';
        $html[] = '<ul class="attachments_list">';

        foreach ($target_users as $target_user)
        {
            if (is_object($target_user) && $target_user instanceof User)
            {
                $target_user = $target_user->get_fullname() . ' &lt;' . $target_user->get_email() . '&gt;';
            }

            $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu/group.png" alt="user"/> ' . $target_user . '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
?>