<?php
/*
 * Class to create a fieldset
 */
class Fieldset extends Container
{
	private $title; //This title will come between the legend tags of the fieldset
	
	/*
	 * Constructor of the class.
	 */
	public function Fieldset($name, $title)
	{
		parent::Container($name);
		$this->title = $title;		
	}
	
	/*
	 * This function renders the fieldset and all of his elements that it contains.
	 */
	public function render()
	{
		$html = array();
		$html[] = sprintf("<fieldset> <legend>".$this->title ."</legend>");
		$arr = $this->get_elements();
		for($i = 0; $i<count($arr); $i++)
		{			
			$html[] = '<div class="row">';
			$html[] = '<div class="label">';
			$temp = $arr[$i]->get_label();
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
		$html[] = sprintf('</fieldset>');
		$html[] = $this->get_errors();
		return implode('', $html); 
	}	
}
?>
