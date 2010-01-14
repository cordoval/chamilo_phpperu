<?php

class DocumentSlideshowSettingsForm extends FormValidator
{
	private $user;
	
	function DocumentSlideshowSettingsForm($action, $user)
	{
		parent :: FormValidator('document_slideshow_settings', 'post', $action);
		$this->user = $user;

		$this->build_basic_form();
	}
	
	function build_basic_form()
	{
		$this->addElement('category', Translation :: get('SlideshowOptions'));

        $group[] = & $this->createElement('radio', 'resize', null, Translation :: get('NoResizing') . '<br />', 0);
        $group[] = & $this->createElement('radio', 'resize', null, Translation :: get('Resize') . ': ', 1);
        $group[] = & $this->createElement('text', 'width', Translation :: get('Width'));
        $group[] = & $this->createElement('text', 'height', Translation :: get('Height'));
        $this->addGroup($group, 'resizing', '', '');
		
		$this->addElement('category');
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->setDefaults();
	}
	
	function update_settings()
	{
		$values = $this->exportValues();
		$values = $values['resizing'];
		if($values['resize'] == 1)
		{
			Session :: register('slideshow_resize', $values['width'] . '|' . $values['height']);
		}
		else
		{
			Session :: unregister('slideshow_resize');
		}
	}
	
	function setDefaults($parameters = array())
	{
    	$parameters['resizing']['resize'] = 0;
    	
    	$resize = Session :: retrieve('slideshow_resize');
    	if($resize)
    	{
    		list($width, $height) = explode('|', $resize);
    		$parameters['resizing']['width'] = $width;
    		$parameters['resizing']['height'] = $height;
    		$parameters['resizing']['resize'] = 1;
    	}
    	
		parent :: setDefaults($parameters);
	}
}

?>