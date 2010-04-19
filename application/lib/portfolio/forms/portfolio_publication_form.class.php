<?php
/**
 * $Id: portfolio_publication_form.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.forms
 */
require_once dirname(__FILE__) . '/../portfolio_publication.class.php';
require_once dirname(__FILE__) . '/../portfolio_rights.class.php';

/**
 * This class describes the form for a PortfolioPublication object.
 * @author Sven Vanpoucke
 **/
class PortfolioPublicationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;


    const RIGHT_VIEW = 'view';
    const RIGHT_EDIT = 'edit';
    const RIGHT_VIEW_FEEDBACK = 'viewFeedback';
    const RIGHT_GIVE_FEEDBACK = 'giveFeedback';
    const INHERIT_OR_SET = 'inherit_set';

    private $portfolio_publication;
    private $user;

    function PortfolioPublicationForm($form_type, $portfolio_publication, $action, $user, $type)
    {
        parent:: __construct('portfolio_publication_settings', 'post', $action);
        $this->portfolio_publication = $portfolio_publication;
        $this->user = $user;
        $this->form_type = $form_type;

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form($type);
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form($type);
        }
      
        //$this->setDefaults();
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
        $attributes1['exclude'] = array('user_' . $this->user->get_id());
        $attributes1['defaults'] = array();
 //       $pub1 = $this->portfolio_publication;
        $udm1 = UserDataManager :: get_instance();
        $gdm1 = GroupDataManager :: get_instance();
        //TODO:SET CURRENT PERMISSION FOR LOCATION IN FORM
//        foreach ($pub1->get_target_users() as $user_id) {
//            $user = $udm1->retrieve_user($user_id);
//            $default = array();
//            $default['id'] = 'user_' . $user_id;
//            $default['classes'] = 'type type_user';
//            $default['title'] = $user->get_fullname();
//            $default['description'] = $user->get_fullname();
//            $attributes1['defaults'][] = $default;
//        }
//        foreach ($pub1->get_target_groups() as $group_id) {
//            $group = $gdm1->retrieve_group($group_id);
//            $default = array();
//            $default['id'] = 'group_' . $group_id;
//            $default['classes'] = 'type type_group';
//            $default['title'] = $group->get_name();
//            $default['description'] = $group->get_name();
//            $attributes1['defaults'][] = $default;
//        }

        $radioOptions = array();
        $i = 0;

        $radioOptions[$i++] = portfolioRights::RADIO_OPTION_ANONYMOUS;
        $radioOptions[$i++] = portfolioRights::RADIO_OPTION_ALLUSERS;
        $radioOptions[$i++] = portfolioRights::RADIO_OPTION_ME;
        
        $rights_array = array();
        if($type == portfolioRights::TYPE_PORTFOLIO_ITEM)
        {
            $rights_array[] = self::RIGHT_VIEW;
            $rights_array[] = self::RIGHT_EDIT;
            $rights_array[] = self::RIGHT_VIEW_FEEDBACK;
            $rights_array[] = self::RIGHT_GIVE_FEEDBACK;
        }
        else
        {
            $rights_array[] = self::RIGHT_VIEW;
            $rights_array[] = self::RIGHT_VIEW_FEEDBACK;
            $rights_array[] = self::RIGHT_GIVE_FEEDBACK;
        }
        if($type == portfolioRights::TYPE_PORTFOLIO_FOLDER)
        {
            $inherit_default = portfolioRights::RADIO_OPTION_DEFAULT;
        }
        else
        {
            $inherit_default = portfolioRights::RADIO_OPTION_INHERIT;
        }

        $this->add_inherit_set_option($rights_array, $inherit_default, $radioOptions, $attributes1, $defaultSelected);

        // $this->add_forever_or_timewindow();
       
    }

    function build_editing_form($type)
    {
        
        $this->build_basic_form($type);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
       
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();
        
//        if ($pub->get_from_date() == 0 && $pub->get_to_date() == 0)
//        {
//            $defaults['forever'] = 1;
//        }
//        else
//        {
//            $defaults['forever'] = 0;
//        }

            if($type == portfolioRights::TYPE_PORTFOLIO_FOLDER)
            {
                $pub = $this->portfolio_publication;
                $rights = portfolioRights::get_all_publication_rights($pub->get_location());
            }
            else
            {
                $cid = Request::get('cid');
                $user_id = Request::get('user_id');
                $location = portfolioRights::get_portfolio_location($cid, $type, $user_id);
                if($location)
                {
                    $rights = portfolioRights::get_all_publication_rights($location);
                }
                else
                {
                    $rights = array();
                }
            }
            
            if(isset($rights[PortfolioPublicationForm::INHERIT_OR_SET]['option']))
            {
                if(($type == portfolioRights::TYPE_PORTFOLIO_FOLDER) && ($rights[PortfolioPublicationForm::INHERIT_OR_SET]['option'] == true))
                {
                    $defaults[self::INHERIT_OR_SET. '_option'] = portfolioRights::RADIO_OPTION_DEFAULT;
                }
                elseif($rights[PortfolioPublicationForm::INHERIT_OR_SET]['option'] == true)
                {
                    $defaults[self::INHERIT_OR_SET. '_option'] = portfolioRights::RADIO_OPTION_INHERIT;
                }
                else
                {
                    $defaults[self::INHERIT_OR_SET. '_option'] = portfolioRights::RADIO_OPTION_SET_SPECIFIC;
                     if(isset($rights[PortfolioPublicationForm::RIGHT_EDIT]['option']))
                    {
                        $defaults[self::RIGHT_EDIT. '_option'] = $rights[PortfolioPublicationForm::RIGHT_EDIT]['option'] ;
                    }
                    else
                    {
                         $defaults[self::RIGHT_VIEW. '_option'] = portfolioRights::RADIO_OPTION_INHERIT ;
                    }
                    if(isset($rights[PortfolioPublicationForm::RIGHT_VIEW]['option']))
                    {
                        $defaults[self::RIGHT_VIEW. '_option'] = $rights[PortfolioPublicationForm::RIGHT_VIEW]['option'];
                    }
                    else
                    {
                        $defaults[self::RIGHT_EDIT. '_option'] = portfolioRights::RADIO_OPTION_INHERIT ;
                    }
                    if(isset($rights[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK]['option']))
                    {
                        $defaults[self::RIGHT_VIEW_FEEDBACK. '_option'] = $rights[PortfolioPublicationForm::RIGHT_VIEW_FEEDBACK]['option'];
                    }
                    else
                    {
                        $defaults[self::RIGHT_VIEW_FEEDBACK. '_option'] = portfolioRights::RADIO_OPTION_INHERIT;
                    }
                    if(isset($rights[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK]['option']))
                    {
                        $defaults[self::RIGHT_GIVE_FEEDBACK. '_option'] = $rights[PortfolioPublicationForm::RIGHT_GIVE_FEEDBACK]['option'];
                    }
                    else
                    {
                        $defaults[self::RIGHT_GIVE_FEEDBACK. '_option'] = portfolioRights::RADIO_OPTION_INHERIT;
                    }
                    //TODO: set users and groups when specific rights are set
                }
            }
          
        parent :: setDefaults($defaults);
    }

    function build_creation_form($type)
    {
        $this->build_basic_form($type);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();
        if($type == portfolioRights::TYPE_PORTFOLIO_FOLDER)
        {
                $inherit_default = portfolioRights::RADIO_OPTION_DEFAULT;
        }
        else
        {
            $inherit_default = portfolioRights::RADIO_OPTION_INHERIT;
        }

        $defaults['inherit_set_option'] = $inherit_default ;

        //$defaults['forever'] = 1;
        parent :: setDefaults($defaults);
    }

    function update_portfolio_publication()
    {
        $portfolio_publication = $this->portfolio_publication;
        $values = $this->exportValues();
//        if ($values['forever'] == 1)
//        {
//            $from = $to = 0;
//        }
//        else
//        {
//            $from = Utilities :: time_from_datepicker($values['from_date']);
//            $to = Utilities :: time_from_datepicker($values['to_date']);
//        }
//        $portfolio_publication->set_from_date($from);
//        $portfolio_publication->set_to_date($to);
//        $portfolio_publication->set_hidden($values[PortfolioPublication :: PROPERTY_HIDDEN]);

        if($type == portfolioRights::TYPE_PORTFOLIO_FOLDER)
            {
                $location = $this->portfolio_publication->get_location();
            }
            else
            {
                $cid = Request::get('cid');
                $user_id = Request::get('user_id');
                $location = portfolioRights::get_portfolio_location($cid, $type, $user_id);

            }

            return portfolioRights::implement_update_rights($values, $location);
    }

    function create_portfolio_publications($objects)
    {
        $values = $this->exportValues();
        
//        if ($values['forever'] == 1)
//        {
//            $from = $to = 0;
//        }
//        else
//        {
//            $from = Utilities :: time_from_datepicker($values['from_date']);
//           $to = Utilities :: time_from_datepicker($values['to_date']);
//        }
        $succes = true;
        foreach ($objects as $object)
        {
            $portfolio_publication = new PortfolioPublication();
            $portfolio_publication->set_content_object($object);
            //$portfolio_publication->set_from_date($from);
            //$portfolio_publication->set_to_date($to);
            $portfolio_publication->set_publisher($this->user->get_id());
            $portfolio_publication->set_published(time());
            $succes &= $portfolio_publication->create();
            if($succes)
            {
                $location = $portfolio_publication->get_location();
            }
            if($location)
            {
                portfolioRights::implement_rights($values, $location);
            }
        }
        return $succes;
    }

//    /**
//     * Sets default values.
//     * @param array $defaults Default values for this form's parameters.
//     */
//    function setDefaults($defaults = array ())
//    {
//        $portfolio_publication = $this->portfolio_publication;
//
//        //$defaults[PortfolioPublication :: PROPERTY_FROM_DATE] = $portfolio_publication->get_from_date();
//        //$defaults[PortfolioPublication :: PROPERTY_TO_DATE] = $portfolio_publication->get_to_date();
//        //$defaults[PortfolioPublication :: PROPERTY_HIDDEN] = $portfolio_publication->get_hidden();
//
//        parent :: setDefaults($defaults);
//    }

    function add_inherit_set_option($rightsarray, $inherit_default, $radio_options, $attributes, $defaultSelected)
    {
        $idSet = portfolioRights::RADIO_OPTION_SET_SPECIFIC;
        $choices[] = $this->createElement('radio', self::INHERIT_OR_SET.'_option', '', Translation::get($inherit_default), $inherit_default, array('onclick'=>'javascript:options_hide()', 'id'=>$inherit_default));
        $choices[] = $this->createElement('radio', self::INHERIT_OR_SET.'_option', '', Translation::get(portfolioRights::RADIO_OPTION_SET_SPECIFIC), portfolioRights::RADIO_OPTION_SET_SPECIFIC, array('onclick'=>'javascript:options_show()', 'id'=>$idSet));
        $this->addGroup($choices, null, Translation::get('inherit_default_set_choice'), '<br/>', false);
        
        $nameWindow = 'options_window';
        $this->addElement('html','<div id="'.$nameWindow.'">');

        foreach ($rightsarray as $right)
        {
            $this->add_receivers_variable($right, Translation :: get($right), $attributes, $radio_options, $defaultSelected);
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


}
?>