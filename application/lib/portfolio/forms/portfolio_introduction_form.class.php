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
class PortfolioIntroductionForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_CREATE_DEFAULTS = 3;



    const RIGHT_VIEW = 'view';
    const RIGHT_EDIT = 'edit';
    const RIGHT_VIEW_FEEDBACK = 'viewFeedback';
    const RIGHT_GIVE_FEEDBACK = 'giveFeedback';
    const INHERIT_OR_SET = 'inherit_set';

   
    private $user;
   

    function PortfolioIntroductionForm($form_type, $action, $user)
    {

        

        parent:: __construct('portfolio_publication_settings', 'post', $action);
        
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
        
      
    }

    function build_basic_form()
    {

         $label= Translation :: get('IntroductionTextLabel');
        $this->add_html_editor("introduction", $label, true);
       
       
    }

    function build_editing_form($type)
    {
        $this->build_basic_form($type);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
       
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $defaults = array();
        $pdm = PortfolioDataManager::get_instance();
        $info = $pdm->retrieve_portfolio_information_by_user($this->user->get_id());


        $defaults['introduction'] =  $info->get_introduction();
        
        parent :: setDefaults($defaults);
    }

    function build_creation_form($type)
    {
        $this->build_basic_form($type);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    

    function create_portfolio_introduction()
    {
        
        $values = $this->exportValues();

        $pdm = PortfolioDataManager::get_instance();
        $info = $pdm->retrieve_portfolio_information_by_user($this->user->get_id());

        $info->set_introduction($values[introduction]);

        return $info->update();

        
    }

    

    
}
?>