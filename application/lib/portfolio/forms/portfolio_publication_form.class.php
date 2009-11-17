<?php
/**
 * $Id: portfolio_publication_form.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.forms
 */
require_once dirname(__FILE__) . '/../portfolio_publication.class.php';

/**
 * This class describes the form for a PortfolioPublication object.
 * @author Sven Vanpoucke
 **/
class PortfolioPublicationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $portfolio_publication;
    private $user;

    function PortfolioPublicationForm($form_type, $portfolio_publication, $action, $user)
    {
        parent :: __construct('portfolio_publication_settings', 'post', $action);
        
        $this->portfolio_publication = $portfolio_publication;
        $this->user = $user;
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        
        $pub = $this->portfolio_publication;
        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();
        
        foreach ($pub->get_target_users() as $user_id)
        {
            $user = $udm->retrieve_user($user_id);
            $default = array();
            $default['id'] = 'user_' . $user_id;
            $default['classes'] = 'type type_user';
            $default['title'] = $user->get_fullname();
            $default['description'] = $user->get_fullname();
            
            $attributes['defaults'][] = $default;
        }
        
        foreach ($pub->get_target_groups() as $group_id)
        {
            $group = $gdm->retrieve_group($group_id);
            $default = array();
            $default['id'] = 'group_' . $group_id;
            $default['classes'] = 'type type_group';
            $default['title'] = $group->get_name();
            $default['description'] = $group->get_name();
            
            $attributes['defaults'][] = $default;
        }
        
        $this->add_receivers('target', Translation :: get('PublishFor'), $attributes);
        
        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', PortfolioPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
    }

    function build_editing_form()
    {
        $pub = $this->portfolio_publication;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', PortfolioPublication :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();
        
        if ($pub->get_from_date() == 0 && $pub->get_to_date() == 0)
        {
            $defaults['forever'] = 1;
        }
        else
        {
            $defaults['forever'] = 0;
        }
        
        if ($pub->get_target_groups() == 0 && $pub->get_target_users() == 0)
        {
            $defaults['target_option'] = 0;
        }
        else
        {
            $defaults['target_option'] = 1;
        }
        
        parent :: setDefaults($defaults);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();
        $defaults['target_option'] = 0;
        $defaults['forever'] = 1;
        parent :: setDefaults($defaults);
    }

    function update_portfolio_publication()
    {
        $portfolio_publication = $this->portfolio_publication;
        $values = $this->exportValues();
        
        if ($values['forever'] == 1)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values['from_date']);
            $to = Utilities :: time_from_datepicker($values['to_date']);
        }
        
        $portfolio_publication->set_from_date($from);
        $portfolio_publication->set_to_date($to);
        $portfolio_publication->set_hidden($values[PortfolioPublication :: PROPERTY_HIDDEN]);
        $portfolio_publication->set_target_groups($values['target_elements']['group']);
        $portfolio_publication->set_target_users($values['target_elements']['user']);
        
        return $portfolio_publication->update();
    }

    function create_portfolio_publications($objects)
    {
        $values = $this->exportValues();
        
        //dump($values); exit();
        

        if ($values['forever'] == 1)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values['from_date']);
            $to = Utilities :: time_from_datepicker($values['to_date']);
        }
        
        $succes = true;
        
        foreach ($objects as $object)
        {
            $portfolio_publication = new PortfolioPublication();
            $portfolio_publication->set_content_object($object);
            $portfolio_publication->set_from_date($from);
            $portfolio_publication->set_to_date($to);
            $portfolio_publication->set_hidden($values[PortfolioPublication :: PROPERTY_HIDDEN]);
            $portfolio_publication->set_publisher($this->user->get_id());
            $portfolio_publication->set_published(time());
            $portfolio_publication->set_target_groups($values['target_elements']['group']);
            $portfolio_publication->set_target_users($values['target_elements']['user']);
            
            $succes &= $portfolio_publication->create();
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
        
        $defaults[PortfolioPublication :: PROPERTY_FROM_DATE] = $portfolio_publication->get_from_date();
        $defaults[PortfolioPublication :: PROPERTY_TO_DATE] = $portfolio_publication->get_to_date();
        $defaults[PortfolioPublication :: PROPERTY_HIDDEN] = $portfolio_publication->get_hidden();
        
        parent :: setDefaults($defaults);
    }
}
?>