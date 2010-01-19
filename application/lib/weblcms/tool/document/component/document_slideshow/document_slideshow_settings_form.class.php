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

        $choices = array();
        $choices[] = & $this->createElement('radio', 'resize', null, Translation :: get('NoResizing'), 0, array('onclick' => 'javascript:window_hide(\'resize_window\')', 'id' => 'resize'));
        $choices[] = & $this->createElement('radio', 'resize', null, Translation :: get('Dimensions'), 1, array('onclick' => 'javascript:window_show(\'resize_window\')'));
        $this->addGroup($choices, 'resizing', Translation :: get('Resize'), '<br />');
        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="resize_window">');
        $this->addElement('text', 'resizing[width]', '', array('class' => 'visual_input width', 'style' => 'width: 50px;'));
        $this->addElement('text', 'resizing[height]', '', array('class' => 'visual_input height', 'style' => 'width: 50px;'));
        $this->addElement('html', '</div>');

        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var resize = document.getElementById('resize');
					if (resize.checked)
					{
						window_hide('resize_window');
					}

					function window_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function window_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");

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
    	else
    	{
    		$parameters['resizing']['width'] = 100;
    		$parameters['resizing']['height'] = 100;
    	}

		parent :: setDefaults($parameters);
	}
}

?>