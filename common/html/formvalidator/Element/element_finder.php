<?php
/**
 * $Id: element_finder.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.formvalidator.Element
 */
require_once 'HTML/QuickForm/text.php';
require_once 'HTML/QuickForm/select.php';
require_once 'HTML/QuickForm/button.php';
require_once 'HTML/QuickForm/hidden.php';
require_once 'HTML/QuickForm/group.php';

/**
 * AJAX-based tree search and multiselect element. Use at your own risk.
 * @author Tim De Pauw
 */
class HTML_QuickForm_element_finder extends HTML_QuickForm_group
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 310;
    
    private static $initialized;
    
    private $search_url;
    
    private $locale;
    
    private $default_collapsed;
    
    private $height;
    private $width;
    
    private $exclude;
    
    private $defaults;

    function HTML_QuickForm_element_finder($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default_values = array (), $options = array())
    {
        parent :: __construct($elementName, $elementLabel);
        $this->_type = 'element_finder';
        $this->_persistantFreeze = true;
        $this->_appendName = false;
        $this->locale = $locale;
        $this->exclude = array();
        $this->height = self :: DEFAULT_HEIGHT;
        $this->width = self :: DEFAULT_WIDTH;
        $this->search_url = $search_url;
        $this->options = $options;
        $this->build_elements();
        $this->setValue($default_values, 0);
    }

    function isCollapsed()
    {
        return $this->isDefaultCollapsed() && ! count($this->getValue());
    }

    function isDefaultCollapsed()
    {
        return $this->default_collapsed;
    }

    function getHeight()
    {
        return $this->height;
    }

    function getWidth()
    {
        return $this->width;
    }

    function excludeElements($excluded_ids)
    {
        $this->exclude = array_merge($this->exclude, $excluded_ids);
    }

    function setDefaultCollapsed($default_collapsed)
    {
        $this->default_collapsed = $default_collapsed;
    }

    function setHeight($height)
    {
        $this->height = $height;
    }

    function setWidth($width)
    {
        $this->height = $width;
    }

    private function build_elements()
    {
        $active_id = 'elf_' . $this->getName() . '_active';
        $inactive_id = 'elf_' . $this->getName() . '_inactive';
        $active_hidden_id = 'elf_' . $this->getName() . '_active_hidden';
        $activate_button_id = $inactive_id . '_button';
        $deactivate_button_id = $active_id . '_button';
        
        $this->_elements = array();
        $this->_elements[] = new HTML_QuickForm_hidden($this->getName() . '_active_hidden', null, array('id' => $active_hidden_id));
        $this->_elements[] = new HTML_QuickForm_text($this->getName() . '_search', null, array('class' => 'element_query', 'id' => $this->getName() . '_search_field'));
        $this->_elements[] = new HTML_QuickForm_button($this->getName() . '_activate', '', array('id' => $activate_button_id, 'disabled' => 'disabled', 'class' => 'activate_elements'));
        $this->_elements[] = new HTML_QuickForm_button($this->getName() . '_deactivate', '', array('id' => $deactivate_button_id, 'disabled' => 'disabled', 'class' => 'deactivate_elements'));
    }

    function getValue()
    {
        return $this->get_active_elements();
    }

    function exportValue($submitValues, $assoc = false)
    {
        if ($assoc)
        {
            return array($this->getName() => $this->getValue());
        }
        return $this->getValue();
    }

    function setValue($value, $element_id = 0)
    {
        $serialized = serialize($value);
        $this->_elements[$element_id]->setValue($serialized);
    }

    function get_active_elements()
    {
        return unserialize($this->_elements[0]->getValue());
    }

    function toHTML()
    {
        /*
		 * 0 active hidden
		 * 1 search
		 * 2 deactivate
		 * 3 activate
		 */
        $html = array();
        
        $id = 'tbl_' . $this->getName();
        
        $html[] = '<div class="element_finder" id="' . $id . '" style="' . ($this->isCollapsed() ? ' display: none;' : '') . '">';
        $html[] = $this->_elements[0]->toHTML();
        
        // Search
        $html[] = '<div class="element_finder_search">';
        $this->_elements[1]->setValue('');
        $html[] = $this->_elements[1]->toHTML();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        // The elements
        $html[] = '<div class="element_finder_elements">';
        
        // Inactive
        $html[] = '<div class="element_finder_inactive">';
        $html[] = '<div id="elf_' . $this->getName() . '_inactive" class="inactive_elements" style="height: ' . $this->getHeight() . 'px; width: ' . $this->getWidth() . 'px; overflow: auto;">';
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
        $html[] = '<div id="elf_' . $this->getName() . '_active" class="active_elements" style="height: ' . $this->getHeight() . 'px; width: ' . $this->getWidth() . 'px; overflow: auto;"></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        // Make sure the elements are all within the div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        // Make sure everything is within the general div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        if ($this->isCollapsed())
        {
            //$html[] = '<input type="button" value="'.htmlentities($this->locale['Display']).'" '.'onclick="document.getElementById(\''.$id.'\').style.display = \'\'; this.style.display = \'none\'; document.getElementById(\''.$this->getName().'_search_field\').focus();" id="'.$this->getName().'_expand_button" />';
            $html[] = '<input type="button" value="' . htmlentities($this->locale['Display']) . '" ' . 'id="' . $this->getName() . '_expand_button" />';
        }
        
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
        
        $html[] = 'var ' . $this->getName() . '_excluded = new Array(' . implode(',', $exclude_ids) . ');';
        
        $load_elements = $this->options['load_elements'];
        $load_elements = (isset($load_elements) && $load_elements == false ? ', loadElements: false' : ', loadElements: true');
        
        $default_query = $this->options['default_query'];
        $default_query = (isset($default_query) && ! empty($default_query) ? ', defaultQuery: "' . $default_query . '"' : '');
        
        $html[] = '$("#' . $id . '").elementfinder({ name: "' . $this->getName() . '", search: "' . $this->search_url . '"' . $load_elements . $default_query . ' });';
        $html[] = '</script>';
        
        return implode("\n", $html);
    }

    function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    function accept($renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }
}
?>