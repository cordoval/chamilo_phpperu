<?php
/**
 * $Id: mailer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/../laika_manager_component.class.php';
require_once dirname(__FILE__) . '/../../laika_utilities.class.php';
require_once dirname(__FILE__) . '/../../forms/laika_mailer_form.class.php';

class LaikaManagerMailerComponent extends LaikaManagerComponent
{
    private $selected_users;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SendMail')));

        if (! LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, 'mailer', 'laika_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $form = new LaikaMailerForm($this, $this->get_user(), $this->get_url());

        if ($form->validate())
        {
            $success = $form->send_mails();

            $this->redirect(($success ? Translation :: get('MailsSent') : Translation :: get('MailsNotSent')), ($success ? false : true), array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME));
        }
        else
        {
            $this->display_header($trail);
            echo $form->display();
            $this->display_footer();
        }
    }

    function get_selected_users()
    {
        $selected_ids = $_GET[LaikaManager :: PARAM_RECIPIENTS];
        $recipients = array();

        if (isset($selected_ids))
        {
            if (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }

            $udm = UserDataManager :: get_instance();
            $condition = new InCondition(User :: PROPERTY_ID, $selected_ids);
            $users = $udm->retrieve_users($condition);

            while ($user = $users->next_result())
            {
                $recipient = array();
                $recipient['id'] = $user->get_id();
                $recipient['classes'] = 'type type_user';
                $recipient['title'] = $user->get_fullname();
                $recipient['description'] = $user->get_username();
                $recipients[$recipient['id']] = $recipient;
            }
        }

        return $recipients;
    }
}
?>