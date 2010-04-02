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


    const TYPE_PORTFOLIO = 'p';
    const TYPE_PORTFOLIO_ITEM = 'pi';

    const RIGHT_VIEW = 'view';
    const RIGHT_EDIT = 'edit';
    const RIGHT_VIEW_FEEDBACK = 'viewFeedback';
    const RIGHT_GIVE_FEEDBACK = 'giveFeedback';


    const RADIO_OPTION_DEFAULT = 'SystemDefaults';
    const RADIO_OPTION_INHERIT = 'inheritFromParent';
    const RADIO_OPTION_ANONYMOUS = 'AnonymousUsers';
    const RADIO_OPTION_ALLUSERS =  'SystemUsers';
    const RADIO_OPTION_ME = 'OnlyMe';

    
    private $portfolio_publication;
    private $user;

    function PortfolioPublicationForm($form_type, $portfolio_publication, $action, $user, $type)
    {
        parent :: __construct('portfolio_publication_settings', 'post', $action);
        
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
        
        $this->setDefaults();
    }

    function build_basic_form($type)
    {
        //publish for
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
        $pub1 = $this->portfolio_publication;
        $udm1 = UserDataManager :: get_instance();
        $gdm1 = GroupDataManager :: get_instance();
        //TODO:deze target users en target groups moeten per actie opgehaald worden
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
        if($type == self::TYPE_PORTFOLIO_ITEM)
        {
            $radioOptions[$i++] = self::RADIO_OPTION_INHERIT;
        }

        $radioOptions[$i++] = self::RADIO_OPTION_DEFAULT;
        $radioOptions[$i++] = self::RADIO_OPTION_ANONYMOUS;
        $radioOptions[$i++] = self::RADIO_OPTION_ALLUSERS;
        $radioOptions[$i++] = self::RADIO_OPTION_ME;

        $this->add_receivers_variable(self::RIGHT_VIEW, Translation :: get('ViewBy'), $attributes1, $radioOptions, $defaultSelected);
        if($type == self::TYPE_PORTFOLIO_ITEM)
        {
            $this->add_receivers_variable(self::RIGHT_EDIT, Translation :: get('EditableBy'), $attributes1, $radioOptions, $defaultSelected);
        }
        $this->add_receivers_variable(self::RIGHT_VIEW_FEEDBACK, Translation :: get('ViewFeedbackBy'), $attributes1, $radioOptions, $defaultSelected);
        $this->add_receivers_variable(self::RIGHT_GIVE_FEEDBACK, Translation :: get('GiveFeedbackBy'), $attributes1, $radioOptions, $defaultSelected);

        // $this->add_forever_or_timewindow();
       // $this->addElement('checkbox', PortfolioPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
    }

    function build_editing_form($type)
    {
        $pub = $this->portfolio_publication;
        
        $this->build_basic_form($type);
        
        //$this->addElement('hidden', PortfolioPublication :: PROPERTY_ID);
        
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
//
//        if ($pub->get_target_groups() == 0 && $pub->get_target_users() == 0)
//        {
            //TODO check moet voor elke instelling gebeuren
            if($type == self::TYPE_PORTFOLIO_ITEM)
            {
                $defaults[self::RIGHT_VIEW. '_option'] = self::RADIO_OPTION_INHERIT ;
                $defaults[self::RIGHT_EDIT. '_option'] = self::RADIO_OPTION_INHERIT ;
                $defaults[self::RIGHT_VIEW_FEEDBACK. '_option'] = self::RADIO_OPTION_INHERIT;
                $defaults[self::RIGHT_GIVE_FEEDBACK. '_option'] = self::RADIO_OPTION_INHERIT;
            }
            else
            {
                $defaults[self::RIGHT_VIEW. '_option'] = self::RADIO_OPTION_DEFAULT ;
                $defaults[self::RIGHT_EDIT. '_option'] = self::RADIO_OPTION_DEFAULT ;
                $defaults[self::RIGHT_VIEW_FEEDBACK. '_option'] = self::RADIO_OPTION_DEFAULT ;
                $defaults[self::RIGHT_GIVE_FEEDBACK. '_option'] = self::RADIO_OPTION_DEFAULT ;
            }
//        }
//        else
//        {
//            //TODO hier moet gechecked worden wat de effectieve instelling is
//            $defaults['target_option'] = self::RADIO_OPTION_DEFAULT;
//        }
        
        parent :: setDefaults($defaults);
    }

    function build_creation_form($type)
    {
        $this->build_basic_form($type);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();

        if($type == self::TYPE_PORTFOLIO_ITEM)
        {
            $defaults[self::RIGHT_VIEW. '_option'] = self::RADIO_OPTION_INHERIT ;
            $defaults[self::RIGHT_EDIT. '_option'] = self::RADIO_OPTION_INHERIT ;
            $defaults[self::RIGHT_VIEW_FEEDBACK. '_option'] = self::RADIO_OPTION_INHERIT;
            $defaults[self::RIGHT_GIVE_FEEDBACK. '_option'] = self::RADIO_OPTION_INHERIT;
        }
        else
        {
            $defaults[self::RIGHT_VIEW. '_option'] = self::RADIO_OPTION_DEFAULT ;
            $defaults[self::RIGHT_EDIT. '_option'] = self::RADIO_OPTION_DEFAULT ;
            $defaults[self::RIGHT_VIEW_FEEDBACK. '_option'] = self::RADIO_OPTION_DEFAULT ;
            $defaults[self::RIGHT_GIVE_FEEDBACK. '_option'] = self::RADIO_OPTION_DEFAULT ;
        }

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
        //update the information here for the different rights
//        $portfolio_publication->set_target_groups($values['target_elements']['group']);
//        $portfolio_publication->set_target_users($values['target_elements']['user']);


        $portfolio_publication->set_target(self::RIGHT_VIEW_FEEDBACK, $values[self::RIGHT_VIEW.'_option'], $values[self::RIGHT_VIEW.'_elements']['group'], $values[self::RIGHT_VIEW.'_elements']['user']);
        $portfolio_publication->set_target(self::RIGHT_VIEW_FEEDBACK, $values[self::RIGHT_EDIT.'_option'], $values[self::RIGHT_EDIT.'_elements']['group'], $values[self::RIGHT_EDIT.'_elements']['user']);
        $portfolio_publication->set_target(self::RIGHT_VIEW_FEEDBACK, $values[self::RIGHT_VIEW_FEEDBACK.'_option'], $values[self::RIGHT_VIEW_FEEDBACK.'_elements']['group'], $values[self::RIGHT_VIEW_FEEDBACK.'_elements']['user']);
        $portfolio_publication->set_target(self::RIGHT_VIEW_FEEDBACK, $values[self::RIGHT_GIVE_FEEDBACK.'_option'], $values[self::RIGHT_GIVE_FEEDBACK.'_elements']['group'], $values[self::RIGHT_GIVE_FEEDBACK.'_elements']['user']);



        return $portfolio_publication->update();
    }

    function create_portfolio_publications($objects)
    {
        $values = $this->exportValues();
        
        //dump($values); exit();
        

//        //if ($values['forever'] == 1)
//        //{
//            //$from = $to = 0;
//        //}
//        else
//        {
//           // $from = Utilities :: time_from_datepicker($values['from_date']);
//           // $to = Utilities :: time_from_datepicker($values['to_date']);
//        }
        
        $succes = true;
        
        foreach ($objects as $object)
        {//TODO deze code moet aangepast worden!!!!
            $portfolio_publication = new PortfolioPublication();
            $portfolio_publication->set_content_object($object);
            //$portfolio_publication->set_from_date($from);
            //$portfolio_publication->set_to_date($to);
            //$portfolio_publication->set_hidden($values[PortfolioPublication :: PROPERTY_HIDDEN]);
            $portfolio_publication->set_publisher($this->user->get_id());
            $portfolio_publication->set_published(time());
            //$portfolio_publication->set_target_groups($values['target_elements']['group']);
            //$portfolio_publication->set_target_users($values['target_elements']['user']);
            
            $succes &= $portfolio_publication->create();

//            //create a location for the portfolio and if necessary the root of the portfolio-tree for this user
//            $user = $this->user->get_id();
//            $portfolio_publication->create_location($user);
//            

            
            //TODO: add rights to the location according to the user's choice

        }
        
        return $succes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $portfolio_publication = $this->portfolio_publication;
        
        //$defaults[PortfolioPublication :: PROPERTY_FROM_DATE] = $portfolio_publication->get_from_date();
        //$defaults[PortfolioPublication :: PROPERTY_TO_DATE] = $portfolio_publication->get_to_date();
        //$defaults[PortfolioPublication :: PROPERTY_HIDDEN] = $portfolio_publication->get_hidden();
        
        parent :: setDefaults($defaults);
    }
}
?>