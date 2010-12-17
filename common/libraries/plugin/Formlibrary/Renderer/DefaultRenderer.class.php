<?php
/*
 * Class to render the elements that were added to the form
 */

class DefaultRenderer extends Render
{	
	/*
	 * Render every element in the elementstorage
	 * For every element call the corresponding function to generate XHTML
	 */
	public function render()
	{		
		$html = array();
		$html[] = sprintf('<script src="http://localhost/chamilo-2.0-beta/plugin/FormLibrary/Includes/jquery/javascripts/jquery.validate.js" type="text/javascript">
        </script>
        <script src="http://localhost/chamilo-2.0-beta/plugin/FormLibrary/Includes/jquery/javascripts/jquery.validation.functions.js" type="text/javascript">
        </script>        
        <script type="text/javascript">
            jQuery(function(){');     
		$storage = $this->form->get_element_storage();
		foreach($storage->get_elements() as $value)
		{							
			if($value instanceof Container || $value instanceof Grouping || $value instanceof Fieldset)
			{
				foreach($value->get_elements() as $subelement)
				{
					$html[] = $subelement->get_rulestorage()->get_javascript();
				}				
			}
			else $html[] = $value->get_rulestorage()->get_javascript();														
		}		
		$html[] = sprintf('}); </script>'); 
		$html[] = sprintf(' <form name="%s" method="%s" action="%s" class="%s" enctype="multipart/form-data">',
		$this->form->get_name(),
		$this->form->get_method(),
        $this->form->get_action(),
        $this->form->get_name()
        );
        
        $req = false;
        $arr = $storage->get_elements();
		for($i = 0; $i<count($arr); $i++)
		{			
			$html[] = '<div class="row">';
			$html[] = '<div class="label">';
			$temp = $arr[$i]->get_label();
			if($arr[$i] instanceof Container || $arr[$i] instanceof Grouping || $arr[$i] instanceof Fieldset)
			{
				foreach($arr[$i]->get_elements() as $subelement)
				{
					$rulestorage = $subelement->get_rulestorage();
					foreach($rulestorage->get_rules() as $rule)
					{				
						if($rule instanceof Required)				
						{
							$req = true;												
						}
					}
				}				
			}
			
			$rulestorage = $arr[$i]->get_rulestorage();
			foreach($rulestorage->get_rules() as $rule)
			{				
				if($rule instanceof Required)				
				{
					$req = true;
					$temp .= '<!-- BEGIN required --><span class="form_required"><img src="' . Theme :: get_common_image_path() . 'action_required.png" alt="*" title ="*"/></span> <!-- END required -->';					
				}
			}
			$html[] = $temp;
			$html[] = '</div>';
			$html[] = '<div class="formw">';
			$html[] = '<div class="element">';
			$html[] = $arr[$i]->render();
			$html[] = '<br/><!-- BEGIN error --><span class="form_error">'.$arr[$i]->get_errors().'</span><!-- END error --></div>';			
			$html[] = '<div class="form_feedback"></div></div>';
			$html[] = '<div class="clear">&nbsp;</div>';
			$html[] = '</div>';			
		}
		if($req)
		{	
			$html[] = '<div class="row">';
			$html[] = '<div class="label">';
			$html[] = '</div>';
			$html[] = '<div class="formw">';			
			$html[] = '<span class="form_required"><img src="' . Theme :: get_common_image_path() . 'action_required.png" alt="*" title ="*"/>required field</span>';
			$html[] = '</div>';	
			$html[] = '</div>';
		}
		$html[] = sprintf('</form>');
		 
		return implode("\n", $html);	
	}
}
?>