<?php
/**
 * $Id: portfolio_publication_form.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.forms
 */
require_once dirname(__FILE__) . '/../portfolio_publication.class.php';
require_once dirname(__FILE__) . '/../rights/portfolio_rights.class.php';

/**
 * This class describes the form for a PortfolioPublication object.
 * @author Sven Vanpoucke
 **/
class PortfolioPublicationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_CREATE_DEFAULTS = 3;



    const RIGHT_VIEW = 'view';
    const RIGHT_EDIT = 'edit';
    const RIGHT_VIEW_FEEDBACK = 'viewFeedback';
    const RIGHT_GIVE_FEEDBACK = 'giveFeedback';
    const INHERIT_OR_SET = 'inherit_set';

    private $portfolio_publication;
    private $user;
    private $rights_array = array();
    private $groups_defaults = array();
    private $inherit_default ;

    function PortfolioPublicationForm($form_type, $portfolio_publication, $action, $user, $type)
    {
        parent:: __construct('portfolio_publication_settings', 'post', $action);
        $this->portfolio_publication = $portfolio_publication;
        $this->user = $user;
        $this->form_type = $form_type;


        $this->rights_array = array();
        if($type == PortfolioRights::TYPE_PORTFOLIO_ITEM)
        {
            $this->rights_array[] = self::RIGHT_VIEW;
            $this->rights_array[] = self::RIGHT_EDIT;
            $this->rights_array[] = self::RIGHT_VIEW_FEEDBACK;
            $this->rights_array[] = self::RIGHT_GIVE_FEEDBACK;
        }
        else
        {
            $this->rights_array[] = self::RIGHT_VIEW;
            $this->rights_array[] = self::RIGHT_VIEW_FEEDBACK;
            $this->rights_array[] = self::RIGHT_GIVE_FEEDBACK;
        }

        if($type == PortfolioRights::TYPE_PORTFOLIO_FOLDER)
        {
            $this->inherit_default = PortfolioRights::RADIO_OPTION_DEFAULT;
        }
        else
        {
            $this->inherit_default = PortfolioRights::RADIO_OPTION_INHERIT;
        }



        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form($type);
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form($type);
        }
        elseif ($this->form_type == self :: TYPE_CREATE_DEFAULTS)
        {
            $this->build_system_defaults_form();
        }
      
    }

    function build_basic_form($type)
    {
       
        $attributes1 = array();
        $attributes1['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale1 = array();
        $locale1['Display'] = Translation :: get('SelectRecipients');
        $locale1['Searching'] = Translation :: get('Searching');
        $locale1['NoResults'] = Translation :: get('NoResults');
        $locale1['Error'] = Translation :: get('Error');
        $attributes1['locale'] = $locale1;
        $attributes1['exclude'] = array('user_' . $this->user->get_id(), 'user_'.PortfolioRights::ANONYMOUS_USERS_ID);
        
        $attributes1['defaults'] = array();


        $radioOptions = array();
        $i = 0;

        $radioOptions[$i++] = PortfolioRights::RADIO_OPTION_ANONYMOUS;
        $radioOptions[$i++] = PortfolioRights::RADIO_OPTION_ALLUSERS;
        $radioOptions[$i++] = PortfolioRights::RADIO_OPTION_ME;

        $radioOptionsLimited = array();
        $i = 0;


        $radioOptionsLimited[$i++] = PortfolioRights::RADIO_OPTION_ALLUSERS;
        $radioOptionsLimited[$i++] = PortfolioRights::RADIO_OPTION_ME;
        
        

        $this->add_inherit_set_option($this->rights_array, $inherit_default, $radioOptions, $radioOptionsLimited, $attributes1, $defaultSelected);


        $this->addElement('html', PortfolioManager::display_system_settings_link());
        $this->addElement('html', PortfolioManager::display_all_portfolio_settings_link());
       
    }

    function build_editing_form($type)
    {
        $defaults = array();
         if($type == PortfolioRights::TYPE_PORTFOLIO_FOLDER)
            {
                $pub = $this->portfolio_publication;
                $rights = PortfolioRights::get_all_publication_rights($pub->get_location());
            }
            else
            {
                $cid = Request::get('cid');
                $user_id = Request::get(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID);
                $location = PortfolioRights::get_portfolio_location($cid, $type, $user_id);
                if($location)
                { //TODO deze rechten ook op de sessie?
                    $rights = PortfolioRights::get_all_publication_rights($location);
                }
                else
                {
                    $rights = array();
                }
            }

//        if(isset($rights[PortfolioPublicationForm::INHERIT_OR_SET]['option']))
//        {
            if(($type == PortfolioRights::TYPE_PORTFOLIO_FOLDER) && ($rights[PortfolioPublicationForm::INHERIT_OR_SET]['option'] == true))
            {
                $defaults[self::INHERIT_OR_SET. '_option'] = PortfolioRights::RADIO_OPTION_DEFAULT;
                 $defaults[$right_type. '_option'] = PortfolioRights::RADIO_OPTION_INHERIT ;

            }
            elseif($rights[PortfolioPublicationForm::INHERIT_OR_SET]['option'] == true)
            {
                $defaults[self::INHERIT_OR_SET. '_option'] = PortfolioRights::RADIO_OPTION_INHERIT;
                 $defaults[$right_type. '_option'] = PortfolioRights::RADIO_OPTION_INHERIT ;
                    
            }
            else
            {
                $defaults[self::INHERIT_OR_SET. '_option'] = PortfolioRights::RADIO_OPTION_SET_SPECIFIC;
                 foreach($this->rights_array as $right_type)
                {
                    if(isset($rights[$right_type]['option']))
                    {
                        $defaults[$right_type. '_option'] = $rights[$right_type]['option'];
                        if($rights[$right_type]['option'] == PortfolioRights::RADIO_OPTION_GROUPS_USERS)
                        {

                            if(isset($rights[PortfolioRights::GROUP_RIGHTS]))
                            {
                                $group_location_array = $rights[PortfolioRights::GROUP_RIGHTS];

                                foreach($group_location_array as $group_location)
                                {
                                    $group_id = $group_location->get_group_id();

                                    if(self::right_id_to_string($group_location->get_right_id()) == $right_type )
                                    {
                                        $gdm = GroupDataManager::get_instance();
                                        $group = $gdm->retrieve_group($group_id);
                                        $group_info = array();
                                        $group_info['id'] = 'group_'.$group_id;
                                        $group_info['classes'] = 'type type_group';
                                        $group_info['title'] = $group->get_name();
                                        $group_info['description'] = $group->get_name();
                                        $this->group_defaults[$right_type][] = $group_info;
                                    }
                                }
                            }
                            if(isset($rights[PortfolioRights::USER_RIGHTS]))
                            {
                                $user_location_array = $rights[PortfolioRights::USER_RIGHTS];
                                foreach($user_location_array as $user_location)
                                {
                                    $user_id = $user_location->get_user_id();

                                    if(self::right_id_to_string($user_location->get_right_id()) == $right_type )
                                    {
                                        $udm = UserDataManager::get_instance();
                                        $user = $udm->retrieve_user($user_id);
                                        $user_info = array();
                                        $user_info['id'] = 'user_'.$user_id;
                                        $user_info['classes'] = 'type type_user';
                                        $user_info['title'] = $user->get_fullname();
                                        $user_info['description'] = $user->get_fullname();
                                        $this->group_defaults[$right_type][] = $user_info;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $group_defaults[$right_type] = array();
                        }

                    }
                    
                       

                }
            }
//        }
        
        $this->build_basic_form($type);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
       
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        
        parent :: setDefaults($defaults);
    }

    function build_creation_form($type)
    {
        $this->build_basic_form($type);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();
        if($type == PortfolioRights::TYPE_PORTFOLIO_FOLDER)
        {
                $this->inherit_default = PortfolioRights::RADIO_OPTION_DEFAULT;
        }
        else
        {
            $this->inherit_default = PortfolioRights::RADIO_OPTION_INHERIT;
        }

        $defaults['inherit_set_option'] = $this->inherit_default ;
     
        parent :: setDefaults($defaults);
    }

    function build_system_defaults_form()
    {
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . PortfolioRights::ANONYMOUS_USERS_ID);
        $attributes['defaults'] = array();
        $attributes['options'] = array();

        $radio_options = array();
        $radio_options[0] = PortfolioRights::RADIO_OPTION_ANONYMOUS;
        $radio_options[1] = PortfolioRights::RADIO_OPTION_ALLUSERS;
        $radio_options[2] = PortfolioRights::RADIO_OPTION_ME;

        $radio_options_limited = array();
        $radio_options_limited[0] = PortfolioRights::RADIO_OPTION_ALLUSERS;
        $radio_options_limited[1] = PortfolioRights::RADIO_OPTION_ME;

        $rights_array = array();

        $rights_array[] = self::RIGHT_VIEW;
        $rights_array[] = self::RIGHT_EDIT;
        $rights_array[] = self::RIGHT_VIEW_FEEDBACK;
        $rights_array[] = self::RIGHT_GIVE_FEEDBACK;
        $group_defaults[self::RIGHT_VIEW] = array();
        $group_defaults[self::RIGHT_EDIT] = array();
        $group_defaults[self::RIGHT_VIEW_FEEDBACK] = array();
        $group_defaults[self::RIGHT_GIVE_FEEDBACK] = array();

        $defaults = array();
        //get the defaultrights

        $location = PortfolioRights::get_default_location();
        if($location)
        {
            $rights = PortfolioRights::get_all_publication_rights($location);
        }
        else
        {
            $rights = array();
        }

        foreach($rights_array as $right_type)
        {
            if(isset($rights[$right_type]['option']))
            {
                $defaults[$right_type. '_option'] = $rights[$right_type]['option'];
                if($rights[$right_type]['option'] == PortfolioRights::RADIO_OPTION_GROUPS_USERS)
                {

                    if(isset($rights[PortfolioRights::GROUP_RIGHTS]))
                    {
                        $group_location_array = $rights[PortfolioRights::GROUP_RIGHTS];

                        foreach($group_location_array as $group_location)
                        {
                            $group_id = $group_location->get_group_id();

                            if(self::right_id_to_string($group_location->get_right_id()) == $right_type )
                            {
                                $gdm = GroupDataManager::get_instance();
                                $group = $gdm->retrieve_group($group_id);
                                $group_info = array();
                                $group_info['id'] = 'group_'.$group_id;
                                $group_info['classes'] = 'type type_group';
                                $group_info['title'] = $group->get_name();
                                $group_info['description'] = $group->get_name();
                                $group_defaults[$right_type][] = $group_info;
                            }
                        }
                    }
                    if(isset($rights[PortfolioRights::USER_RIGHTS]))
                    {
                        $user_location_array = $rights[PortfolioRights::USER_RIGHTS];
                        foreach($user_location_array as $user_location)
                        {
                            $user_id = $user_location->get_user_id();

                            if(self::right_id_to_string($user_location->get_right_id()) == $right_type )
                            {
                                $udm = UserDataManager::get_instance();
                                $user = $udm->retrieve_user($user_id);
                                $user_info = array();
                                $user_info['id'] = 'user_'.$user_id;
                                $user_info['classes'] = 'type type_user';
                                $user_info['title'] = $user->get_fullname();
                                $user_info['description'] = $user->get_fullname();
                                $group_defaults[$right_type][] = $user_info;
                            }
                        }
                    }
                }

            }
        }




        foreach ($rights_array as $right)
        {
            $attributes['defaults'] = $group_defaults[$right];

             if($right != self::RIGHT_GIVE_FEEDBACK && $right != self::RIGHT_EDIT)
            {
                $this->add_receivers_variable($right, Translation :: get($right), $attributes, $radio_options, PortfolioRights::RADIO_OPTION_ALLUSERS);
            }
            else
            {
               $this->add_receivers_variable($right, Translation :: get($right), $attributes, $radio_options_limited, PortfolioRights::RADIO_OPTION_ALLUSERS);
            }

                
        }


        $this->addElement('html', "<script type = \"text/javascript\">
                                    /* <![CDATA[ */
                                    var no_inherit = document.getElementById('$idSet');
                                    if(no_inherit.checked)
                                    {
                                        options_show();
                                    }
                                    else
                                    {
                                        options_hide();
                                    }
                                    function options_show()
                                    {
                                        el= document.getElementById('$nameWindow');
                                        el.style.display='';
                                    }
                                    function options_hide()
                                    {
                                        el= document.getElementById('$nameWindow');
                                        el.style.display='none';
                                    }
                                    /* ]]> */
                                    </script>\n"
                        );

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SetSystemDefaults'), array('class' => 'positive'));
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        
         
        

        parent :: setDefaults($defaults);
    }

    function create_portfolio_default_settings()
    {
        $values = $this->exportValues();
        return PortfolioRights::implement_default_rights($values);

    }

    function update_portfolio_publication($type)
    {
        $portfolio_publication = $this->portfolio_publication;
        $values = $this->exportValues();

        if($type == PortfolioRights::TYPE_PORTFOLIO_FOLDER)
        {
                $location = $portfolio_publication->get_location();
        }
        else
        {
            $cid = Request::get('cid');
            $user_id = Request::get(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID);
            $location = PortfolioRights::get_portfolio_location($cid, $type, $user_id);

        }

        return PortfolioRights::implement_update_rights($values, $location);
    }

    function create_portfolio_publications($object_ids, $owner_id = null)
    {
        $values = $this->exportValues();

        $success = true;

        foreach ($object_ids as $object_id)
        {

            $portfolio_publication = new PortfolioPublication();
            $portfolio_publication->set_content_object($object_id);
            $portfolio_publication->set_publisher($this->user->get_id());
            
            if($owner_id == null)
            {
                //owner is  the same user as publisher
               $owner_id =  $this->user->get_id();
            }
            
            $portfolio_publication->set_owner($owner_id);
            $portfolio_publication->set_published(time());
            $success &= $portfolio_publication->create();
            if($success)
            {
                $location = $portfolio_publication->get_location();
                $info = PortfolioManager::get_portfolio_info($owner_id);
                if($location)
                {
                     $success &= PortfolioRights::implement_rights($values, $location);
                }
                if($info)
                {
                    $info->set_last_updated_date(time());
                    $info->set_last_updated_item_id($object_id);
                    $info->set_last_updated_item_type(PortfolioRights::TYPE_PORTFOLIO_FOLDER);
                    $info->set_last_action(PortfolioInformation::ACTION_PORTFOLIO_ADDED);
                    $success &= $info->update();
                }
                else
                {
                    $info = new PortfolioInformation();
                    $info->set_user_id($owner_id);
                    $info->set_last_updated_date(time());
                    $info->set_last_updated_item_id($object_id);
                    $info->set_last_updated_item_type(PortfolioRights::TYPE_PORTFOLIO_FOLDER);
                    $info->set_last_action(PortfolioInformation::ACTION_FIRST_PORTFOLIO_CREATED);
                    $success &= $info->create();
                }
            }


        }
        return $success;
    }



    function add_inherit_set_option($rightsarray, $inherit_default, $radio_options, $radio_options_limited, $attributes, $defaultSelected)
    {
        $idSet = PortfolioRights::RADIO_OPTION_SET_SPECIFIC;
        $choices[] = $this->createElement('radio', self::INHERIT_OR_SET.'_option', '', Translation::get($this->inherit_default), $this->inherit_default, array('onclick'=>'javascript:options_hide()', 'id'=>$this->inherit_default));
        $choices[] = $this->createElement('radio', self::INHERIT_OR_SET.'_option', '', Translation::get(PortfolioRights::RADIO_OPTION_SET_SPECIFIC), PortfolioRights::RADIO_OPTION_SET_SPECIFIC, array('onclick'=>'javascript:options_show()', 'id'=>$idSet));
        $this->addGroup($choices, null, Translation::get('inherit_default_set_choice'), '<br/>', false);
        
        $nameWindow = 'options_window';
        $this->addElement('html','<div id="'.$nameWindow.'">');

        foreach ($rightsarray as $right)
        {
            $defaults = $this->group_defaults[$right];
            if(!isset($defaults))
            {
                $defaults = array();
            }
            $attributes['defaults'] = $defaults;

            if($right != self::RIGHT_GIVE_FEEDBACK && $right != self::RIGHT_EDIT)
            {
                $this->add_receivers_variable($right, Translation :: get($right), $attributes, $radio_options, $defaultSelected);
            }
            else
            {
               $this->add_receivers_variable($right, Translation :: get($right), $attributes, $radio_options_limited, $defaultSelected);
            }
        }
            
        $this->addElement('html', '</div>');

        $this->addElement('html', "<script type = \"text/javascript\">
                                    /* <![CDATA[ */
                                    var no_inherit = document.getElementById('$idSet');
                                    if(no_inherit.checked)
                                    {
                                        options_show();
                                    }
                                    else
                                    {
                                        options_hide();
                                    }
                                    function options_show()
                                    {
                                        el= document.getElementById('$nameWindow');
                                        el.style.display='';
                                    }
                                    function options_hide()
                                    {
                                        el= document.getElementById('$nameWindow');
                                        el.style.display='none';
                                    }
                                    /* ]]> */
                                    </script>\n"
                        );
    }

    function right_id_to_string($right_id)
    {
        switch ($right_id)
        {
            case PortfolioRights::VIEW_RIGHT:
                $right_string = self::RIGHT_VIEW;
                break;
            case PortfolioRights::EDIT_RIGHT:
                $right_string = self::RIGHT_EDIT;
                break;
            case PortfolioRights::VIEW_FEEDBACK_RIGHT:
                $right_string = self::RIGHT_VIEW_FEEDBACK;
                break;
            case PortfolioRights::GIVE_FEEDBACK_RIGHT:
                $right_string = self::RIGHT_GIVE_FEEDBACK;
                break;
            default :
                $right_string = '';
        }
        return $right_string;

    }

    
}
?>