<?php
/**
 * $Id: builder.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../dynamic_form_element.class.php';
require_once dirname(__FILE__) . '/dynamic_form_element_browser/dynamic_form_element_browser_table.class.php';

class DynamicFormManagerBuilderComponent extends DynamicFormManagerComponent
{
    function run()
    {
    	$trail = new BreadcrumbTrail(false);
    	$this->display_header($trail);
    	
    	$this->display_element_types();
    	echo '<br /><br />';
    	$this->display_element_table();
    	
    	$this->display_footer();
    }
    
    function display_element_table()
    {
    	$table = new DynamicFormElementBrowserTable($this, $this->get_parameters(), null);
    	echo $table->as_html();
    }
    
    function display_element_types()
    {
    	$html[] = '<div class="category_form"><div id="content_object_selection">';
    	
    	foreach(DynamicFormElement :: get_types() as $typename => $typevalue)
    	{
    		$link = $this->get_url(array(DynamicFormManager :: PARAM_DYNAMIC_FORM_ACTION => DynamicFormManager :: ACTION_ADD_FORM_ELEMENT, 
    								     DynamicFormManager :: PARAM_DYNAMIC_FORM_ELEMENT_TYPE => $typevalue));
    		$html[] = '<a href="' . $link . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/form_type_' . $typevalue . '.png);">';
            $html[] = $typename;
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
    	}
    	
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        echo implode("\n", $html);
    }
}
?>