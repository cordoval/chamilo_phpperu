<?php
/**
 * $Id: right_requester.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_manager.component
 */

class RightsManagerRightRequesterComponent extends RightsManagerComponent
{
    const USER_CURRENT_RIGHTS_TEMPLATES = 'USER_CURRENT_RIGHTS_TEMPLATES';
    const USER_CURRENT_GROUPS = 'USER_CURRENT_GROUPS';
    const IS_NEW_USER = 'IS_NEW_USER';
    
    const PARAM_IS_NEW_USER = 'newUser';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        
        if (isset($user))
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_REQUEST_RIGHT)), Translation :: get('RightRequest')));
            
            $parameters = array();
            
            $parameters['form_action'] = $this->get_url(array(self :: PARAM_IS_NEW_USER => '1'));
            
            $new_profile = Request :: get(self :: PARAM_IS_NEW_USER);
            if (isset($new_profile))
            {
                $parameters[self :: IS_NEW_USER] = true;
            }
            
            $form = new RightsTemplateRequestForm($parameters);
            
            if ($form->validate())
            {
                $this->display_header($trail);
                
                $data = $form->getSubmitValues();
                
                //set the Translation language to the platform default language for the email to the Chamilo administrator
                $traductor = Translation :: get_instance();
                $traductor->set_language(PlatformSetting :: get_instance()->get('platform_language'));
                
                $admin_email = PlatformSetting :: get_instance()->get('administrator_email');
                $email_content = Security :: remove_XSS($data[RightsTemplateRequestForm :: REQUEST_CONTENT]);
                $email_title = $traductor->get('RightRequestEmailTitle');
                $email_user = $user->get_email();
                $email_username = $user->get_lastname() . ' ' . $user->get_firstname();
                $user_id = $user->get_id();
                $email_body = $traductor->get('RightRequestEmailBody');
                $email_body = sprintf($email_body, $email_username, $user_id, $email_content);
                
                //reset the Translation language to the user preference
                $traductor->set_language($user->get_language());
                
                $mail = Mail :: factory($email_title, $email_body, $admin_email, $admin_email, array($email_user));
                if ($mail->send())
                {
                    $form->print_request_successfully_sent();
                }
                else
                {
                    $form->print_request_sending_error();
                }
                
                $this->display_footer();
            }
            else
            {
                /*
    	         * display request form
    	         * 
    	         * - get user's current rights_templates and groups (to display them to the user) 
    	         */
                
                $this->display_header($trail);
                
                $rights_templates = array();
                $groups = array();
                $user_rights_templates = $user->get_rights_templates();
                $user_groups = $user->get_user_groups();
                
                $gdm = GroupDataManager :: get_instance();
                while ($user_group = $user_groups->next_result())
                {
                    $group_id = $user_group->get_group_id();
                    $group = $gdm->retrieve_group($group_id);
                    
                    //$group may be null if no FK exists in the DB 
                    if (isset($group))
                    {
                        $groups[] = $group;
                    }
                }
                
                $rdm = RightsDataManager :: get_instance();
                while ($user_rights_template = $user_rights_templates->next_result())
                {
                    $rights_template_id = $user_rights_template->get_rights_template_id();
                    $rights_template = $rdm->retrieve_rights_template($rights_template_id);
                    
                    //$rights_template may be null if no FK exists in the DB
                    if (isset($rights_template))
                    {
                        $rights_templates[] = $rights_template;
                    }
                }
                
                $form->set_parameter(self :: USER_CURRENT_RIGHTS_TEMPLATES, $rights_templates);
                $form->set_parameter(self :: USER_CURRENT_GROUPS, $groups);
                
                $form->print_form_header();
                $form->display();
                
                $this->display_footer();
            }
        }
        else
        {
            Display :: not_allowed();
        }
    }
}
?>