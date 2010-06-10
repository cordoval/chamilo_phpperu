<?php
/**
 * The list of object types you can create: competency, indicator and criteria. 
 * 
 * @author Nick Van Loocke
 */
class CbaManagerCreateComponent extends CbaManager
{
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
		$this->display_header($trail, false, true);
		
		
		// Several object types
		
		$types = array();
		$types['0'] = 'Competency';
		$types['1'] = 'Indicator';
		$types['2'] = 'Criteria';
	
				
		// First list (dropdown list)
		
		$extra_params = array();
		$types_dropdown = array();		
		$types_dropdown['0'] = '-- ' . Translation :: get('SelectObject') . ' --';
		for($i = 1; $i <= sizeof($types); $i++)
		{
			$types_dropdown[$i] = '-- ' . Translation :: get($types[$i - 1]) . ' --';	
		}
        
		$type_form = new FormValidator('create_type', 'post', $this->get_url($extra_params));

        $type_form->addElement('select', CbaManager :: PARAM_CONTENT_OBJECT_TYPE, Translation :: get('CreateANew'), $types_dropdown, array('class' => 'learning-object-creation-type postback'));
        $type_form->addElement('style_submit_button', 'submit', Translation :: get('Select'), array('class' => 'normal select'));
        $type_form->addElement('html', '<br /><br />' . ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/postback.js'));
        
        $type = ($type_form->validate() ? $type_form->exportValue(CbaManager :: PARAM_CONTENT_OBJECT_TYPE) : Request :: get(CbaManager :: PARAM_CONTENT_OBJECT_TYPE));        
        $type_value = strtolower($types[$type - 1]);
        
        if($type_value == Competency :: get_type_name())
        {
        	$this->simple_redirect(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_COMPETENCY, 'type' => $type_value));
        }
        else if($type_value == Indicator :: get_type_name())
        {
        	$this->simple_redirect(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_INDICATOR, 'type' => $type_value));
         }
        else if($type_value == Criteria :: get_type_name())
        {
        	$this->simple_redirect(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_CRITERIA, 'type' => $type_value));
        }           
               
		$renderer = clone $type_form->defaultRenderer();
        $renderer->setElementTemplate('{label}&nbsp;{element}&nbsp;');
        $type_form->accept($renderer);
        echo $renderer->toHTML();
        
        
        // Second list (with images)
        
        echo '<h3>' . Translation :: get('GeneralObjectTypes') . '</h3>';
        
        $types_list = array();
        for($i = 0; $i <= 2; $i++)
        {
        	if($i == 0)
        	{
        		$types_list[] = '<a href="' . $this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_COMPETENCY, 'type' => strtolower($types[$i]))). '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . strtolower($types[$i]) . '.png);">';
        	}
        	elseif($i == 1)
        	{
        		$types_list[] = '<a href="' . $this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_INDICATOR, 'type' => strtolower($types[$i]))). '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . strtolower($types[$i]) . '.png);">';  	
        	}	
        	elseif($i == 2)
        	{
        		$types_list[] = '<a href="' . $this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_CREATOR_CRITERIA, 'type' => strtolower($types[$i]))). '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . strtolower($types[$i]) . '.png);">';
        	
        	}
        	$types_list[] = Translation :: get(ContentObject :: type_to_class($types[$i]) . 'TypeName');
        	$types_list[] = '</div></a>';
        }
        
        foreach($types_list as $object_type_image => $type_image)
        {
        	echo $type_image;
        }  

        $this->display_footer();
	}
	
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}
}

?>