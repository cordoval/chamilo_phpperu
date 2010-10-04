<?php
/**
 * $Id: validation_form.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.validation_manager
 */

/**
 * Description of feeback_text_formclass
 *
 * @author pieter
 */


class ValidationForm extends FormValidator
{
    
    private $adm;

    function ValidationForm($action)
    {
        parent :: __construct('validation_form', 'post', $action);
        $this->build_form();
        
        $this->adm = AdminDataManager :: get_instance();
    
    }

    function build_form()
    {
        //$this->createElement('style_submit_button', 'submit', Translation :: get('Validate'), array('class' => 'positive'));
        // $this->add_html_editor( FeedbackPublication :: PROPERTY_TEXT, 'comment', 'required');
        // $this->addRule( FeedbackPublication :: PROPERTY_TEXT, Translation :: get('ThisFieldIsRequired'), 'required');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Validate'), array('class' => 'positive'));
        // $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_validation($owner, $pid, $cid, $application)
    {
        
        $val = new Validation();
        $val->set_cid($cid);
        $val->set_pid($pid);
        $val->set_application($application);
        $val->set_owner($owner);
        $today = date('Y-m-d G:i:s');
        $val->set_validated($today);
        
        //echo 'pid -> '.$pid.' - cid ->'.$cid;
        return $fb->create();
    }

    function render_content_object($object)
    {
    }

}
?>