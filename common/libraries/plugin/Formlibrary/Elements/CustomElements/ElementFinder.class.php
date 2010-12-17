<?php
/**
 * $Id: element_finder.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.formvalidator.Element
 */
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Text.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Select.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Hidden.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Button.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/User_Group_finder.class.php';

class ElementFinder extends Grouping
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 292;
    
    private static $initialized;
   	private $search_url;    
    private $locale;    
    private $default_collapsed;    
    private $height;
    private $width;    
    private $exclude;    
    private $defaults;
    private $hidden;
    private $text;    

    public function ElementFinder($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default_values = array (), $options = array())
    {
       	parent::Grouping($elementName, $elementLabel, false);
        $this->locale = $locale;
        $this->exclude = array();
        $this->height = self :: DEFAULT_HEIGHT;
        $this->width = self :: DEFAULT_WIDTH;
        $this->search_url = $search_url;
        $this->options = $options;
        $this->build_elements();
        $this->set_value($default_values, $hidden);
    }

    public function isCollapsed()
    {
        return $this->isDefaultCollapsed() && ! count($this->get_value());
    }

    public function isDefaultCollapsed()
    {
        return $this->default_collapsed;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function excludeElements($excluded_ids)
    {
        $this->exclude = array_merge($this->exclude, $excluded_ids);
    }

    public function setDefaultCollapsed($default_collapsed)
    {
        $this->default_collapsed = $default_collapsed;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function setWidth($width)
    {
        $this->height = $width;
    }

    private function build_elements()
    {
        $active_id = 'elf_' . $this->get_name() . '_active';
        $inactive_id = 'elf_' . $this->get_name() . '_inactive';
        $active_hidden_id = 'elf_' . $this->get_name() . '_active_hidden';
        $activate_button_id = $inactive_id . '_button';
        $deactivate_button_id = $active_id . '_button';       
    	
    	$hidden = new Hidden($this->form, 'elf_' . $this->get_name() . '_active_hidden');
    	$text = new Text($this->form, $this->get_name(). '_search_field', "", "");
    	$button1 = new Button($this->form, $activate_button_id, "");
    	$at1 = new Disabled();
    	$button1->get_attributestorage()->add_attribute($at1);
    	$button2 = new Button($this->form, $deactivate_button_id, "");
    	$at2 = new Disabled();
    	$button2->get_attributestorage()->add_attribute($at2);
    	$this->elements[] = $hidden;
    	$this->elements[] = $text;
    	$this->elements[] = $button1;
    	$this->elements[] = $button2;
    }
    
    public function get_value()
    {
    	if(!empty($_POST[$this->get_name()]))
    		return $_POST[$this->get_name()];
    }

    public function set_value($value, $element)
    {
	    if($element != 0)
	    {
	    	$serialized = serialize($value);
	    	$this->elements[$element]->set_value($serialized);        	
	    }
	    else 
	    {
	    	$serialized = serialize($value);
	    	$this->elements[0]->set_value($serialized);
	    }           
    }

    public function get_active_elements()
    {
        return unserialize($this->elements[0]->get_value());
    }    

   	public function render()
    {
        $this->default_collapsed = true;
        /*
		 * 0 active hidden
		 * 1 search
		 * 2 deactivate
		 * 3 activate
		 */
        $html = array();        
        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $this->get_name() . '_expand_button" class="normal select">' . htmlentities($this->locale['Display']) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->get_name() . '_expand_button" style="display: none" class="normal select">' . htmlentities($this->locale['Display']) . '</button>';
        }
        
        $id = 'tbl_' . $this->get_name();
        
        $html[] = '<div class="element_finder" id="' . $id . '" style="margin-top: 5px;' . ($this->isCollapsed() ? ' display: none;' : '') . '">';        
        $html[] = $this->elements[0]->render();
        
        // Search
        $html[] = '<div class="element_finder_search">';
        
        $this->elements[1]->set_value('');
        $html[] = $this->elements[1]->render();
       
        
        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $this->get_name() . '_collapse_button" style="display: none" class="normal hide">' . htmlentities(Translation :: get('Hide')) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->get_name() . '_collapse_button" class="normal hide mini">' . htmlentities(Translation :: get('Hide')) . '</button>';
        }
        
        $html[] = '</div>';
        
        $html[] = '<div class="clear"></div>';
        
        // The elements
        $html[] = '<div class="element_finder_elements">';
        
        // Inactive
        $html[] = '<div class="element_finder_inactive">';
        $html[] = '<div id="elf_' . $this->get_name() . '_inactive" class="inactive_elements" style="height: ' . $this->getHeight() . 'px; width: ' . $this->getWidth() . 'px; overflow: auto;">';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        // Buttons
        //		$html[] = '<div class="element_finder_buttons" style="height: '.$this->getHeight().'px;">';
        //		$html[] = '<div class="button_elements" style="margin-top: '. (($this->height - 46) / 2) .'px">';
        //		$html[] = $this->_elements[2]->toHTML();
        //		$html[] = '<br />';
        //		$html[] = $this->_elements[3]->toHTML();
        //		$html[] = '</div>';
        //		$html[] = '<div class="clear"></div>';
        //		$html[] = '</div>';
        

        // Active
        $html[] = '<div class="element_finder_active">';
        $html[] = '<div id="elf_' . $this->get_name() . '_active" class="active_elements" style="height: ' . $this->getHeight() . 'px; width: ' . $this->getWidth() . 'px; overflow: auto;"></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        // Make sure the elements are all within the div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        // Make sure everything is within the general div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.elementfinder.js');
        $html[] = '<script type="text/javascript">';
        
        $exclude_ids = array();
        if (count($this->exclude))
        {
            $exclude_ids = array();
            foreach ($this->exclude as $exclude_id)
            {
                $exclude_ids[] = "'$exclude_id'";
            }
        }
        
        $html[] = 'var ' . $this->get_name() . '_excluded = new Array(' . implode(',', $exclude_ids) . ');';
        
        $load_elements = $this->options['load_elements'];
        $load_elements = (isset($load_elements) && $load_elements == false ? ', loadElements: false' : ', loadElements: true');
        
        $default_query = $this->options['default_query'];
        $default_query = (isset($default_query) && ! empty($default_query) ? ', defaultQuery: "' . $default_query . '"' : '');
        
        $html[] = '$("#' . $id . '").elementfinder({ name: "' . $this->get_name() . '", search: "' . $this->search_url . '"' . $load_elements . $default_query . ' });';
        $html[] = '</script>';
        
        return implode("\n", $html);
    }
}
?>