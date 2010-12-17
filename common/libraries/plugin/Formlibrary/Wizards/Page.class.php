<?php
require_once Path:: get_plugin_path() . 'FormLibrary/FormLibrary.class.php';

/*
 * Via this class you can create a page for a wizard
 * It inherits from the formlibrary class
 */
class Page extends FormLibrary{
   
    /*
     * Constructor for the class
     */
	public function Page($formName, $method = 'post', $action)
    {
        parent::FormLibrary($formName, $method, $action);        
    }  
    
    /*
     * This function returns the post values of this page
     */
	public function get_values()
    {
    	return $_POST;
    }
    
    /*
     * Via this function, you can add the javascript code for a previous button to the page
     * The validation will be ignored when this code is inlcuded on the page
     */
	public function add_previous_action()
    {
    	$script[] = '<script type="text/javascript">';
		$script[] = '$(".previous").click(function() {'; 
		$script[] = '$(":input").unbind();';
		$script[] = '$(".previous").click();});';
    	$script[] = '</script>';
        $html = new AddHtml($this, $script);
        $this->add_element($html);
    }
}
?>