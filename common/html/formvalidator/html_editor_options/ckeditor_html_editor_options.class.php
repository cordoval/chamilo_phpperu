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
								parent :: OPTION_HEIGHT			    => 'height',
								parent :: OPTION_COLLAPSE_TOOLBAR	=> 'toolbarStartupExpanded',
								parent :: OPTION_CONFIGURATION		=> 'customConfig',
								parent :: OPTION_FULL_PAGE			=> 'fullPage',
								parent :: OPTION_TEMPLATES			=> 'templates');
	/**
     *
     */
    function render_options()
    {
        $javascript = array();
        $available_options = $this->get_option_names();

        foreach($available_options as $available_option)
        {
            if(key_exists($available_option, $this->mapping))
            {
                $value = $this->get_option($available_option);

                if($value)
                {
                    $processing_function = 'process_' . $available_option;
                    if (method_exists($this, $processing_function))
                    {
                        $value = call_user_func(array($this, $processing_function), $value);
                        $javascript[] = '			' . $this->mapping[$available_option] . ' : \''. $value .'\'';
                    }
                    else
                    {
                        $javascript[] = '			' . $this->mapping[$available_option] . ' : \''. $value .'\'';
                    }
                }
            }
        }

    }
}

?>