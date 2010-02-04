<?php
/**
 * The combination of options available for the FormValidatorCkeditorHtmlEditor
 * 
 * @author Scaramanga
 */

class FormValidatorCkeditorHtmlEditorOptions extends FormValidatorHtmlEditorOptions
{
	private $mapping = array(	parent :: OPTION_TOOLBAR			=> 'toolbar',
								parent :: OPTION_LANGUAGE			=> 'defaultLanguage',
								parent :: OPTION_THEME				=> 'theme',
								parent :: OPTION_WIDTH				=> 'width',
								parent :: OPTION_HEIGHT			=> 'height',
								parent :: OPTION_COLLAPSE_TOOLBAR	=> 'toolbarStartupExpanded',
								parent :: OPTION_CONFIGURATION		=> 'customConfig',
								parent :: OPTION_FULL_PAGE			=> 'fullPage',
								parent :: OPTION_ENTER_MODE		=> 'enterMode',
								parent :: OPTION_SHIFT_ENTER_MODE	=> 'shiftEnterMode',
								parent :: OPTION_TEMPLATES			=> 'templates');
	/**
     * 
     */
    function process_options()
    {
        $available_options = $this->get_option_names();
        
        
    }
}

?>