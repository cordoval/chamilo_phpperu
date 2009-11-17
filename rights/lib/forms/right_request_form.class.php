<?php
/**
 * $Id: right_request_form.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.data_manager.forms
 */
class RightsTemplateRequestForm extends FormValidator
{
    const REQUEST_CONTENT = 'REQUEST_CONTENT';
    
    private $parameter = array();

    function RightsTemplateRequestForm($parameters = null)
    {
        parent :: __construct('right_request', 'post', $parameters['form_action']);
        
        $this->parameters = $parameters;
        
        $this->build_request_form();
    }

    function set_parameter($parameter_name, $value)
    {
        if (! isset($this->parameters))
        {
            $this->parameters = array();
        }
        
        $this->parameters[$parameter_name] = $value;
    }

    function build_request_form()
    {
        $this->addElement('textarea', self :: REQUEST_CONTENT, Translation :: get('RightRequestContent'), array('style' => 'width:500px;height:200px;'));
        $this->addRule(self :: REQUEST_CONTENT, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Send'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->setDefaults();
    }

    function print_form_header()
    {
        echo '<div class="row">';
        echo '<div class="formw">';
        echo '<div style="width:500px;text-align:justify">';
        
        $explanation = 'RightRequestExplanationCurrentRights';
        if (isset($this->parameters[RightsManagerRightRequesterComponent :: IS_NEW_USER]) && $this->parameters[RightsManagerRightRequesterComponent :: IS_NEW_USER] == true)
        {
            $explanation = 'RightRequestExplanationCurrentRightsNewUser';
        }
        
        echo '<p>' . Translation :: translate($explanation) . '</p>';
        
        $this->print_rights_templates_list();
        $this->print_groups_list();
        
        echo '<p>' . Translation :: translate('RightRequestExplanationFillForm') . '</p>';
        
        echo '</div></div></div>';
    }

    function print_rights_templates_list()
    {
        if (isset($this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_RIGHTS_TEMPLATES]) && count($this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_RIGHTS_TEMPLATES]) > 0)
        {
            echo '<h4>' . Translation :: translate('RightsTemplates') . '</h4>';
            
            $rights_templates = $this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_RIGHTS_TEMPLATES];
            
            /*
             * Display current user rights_templates 
             */
            echo '<ul>';
            foreach ($rights_templates as $rights_template)
            {
                echo '<li>' . $rights_template->get_name() . '</li>';
            }
            echo '</ul>';
        }
    }

    function print_groups_list()
    {
        if (isset($this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_GROUPS]) && count($this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_GROUPS]) > 0)
        {
            echo '<h4>' . Translation :: translate('Groups') . '</h4>';
            
            $groups = $this->parameters[RightsManagerRightRequesterComponent :: USER_CURRENT_GROUPS];
            
            /*
             * Display current user groups 
             */
            
            echo '<ul>';
            foreach ($groups as $group)
            {
                echo '<li>' . $group->get_name() . '</li>';
            }
            echo '</ul>';
        }
    }

    function print_request_successfully_sent()
    {
        echo '<div class="row">';
        echo '<div class="formw">';
        echo '<div style="width:500px;text-align:justify">';
        
        echo '<p>' . Translation :: translate('RightRequestSuccessfullySent') . '</p>';
        
        echo '</div></div></div>';
    }

    function print_request_sending_error()
    {
        echo '<div class="row">';
        echo '<div class="formw">';
        echo '<div style="width:500px;text-align:justify">';
        
        echo '<p>' . Translation :: translate('RightRequestSendingError') . '</p>';
        
        echo '</div></div></div>';
    }
}
?>