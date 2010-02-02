<?php
/**
 * @package application.lib.cda.forms
 */

class TranslationImportForm extends FormValidator
{
    private $parent;

    /**
     * Creates a new UserForm
     * Used by the admin to create/update a user
     */
    function TranslationImportForm($parent, $action)
    {
        parent :: __construct('translation_import_form', 'post', $action);
        
        $this->parent = $parent;
        
        $this->build_basic_form();
    }

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
        $this->addElement('select', LanguagePack :: PROPERTY_BRANCH, Translation :: get('Branch'), LanguagePack :: get_branch_options());
        
    	$this->addElement('file', 'file', Translation :: get('FileName'));
        $allowed_upload_types = array('zip');
        $this->addRule('file', Translation :: get('OnlyZIPAllowed'), 'filetype', $allowed_upload_types);
        $this->addRule('file', Translation :: get('ThisFieldIsRequired'), 'required');
        
        // Submit button
        //$this->addElement('submit', 'user_settings', 'OK');
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

}
?>