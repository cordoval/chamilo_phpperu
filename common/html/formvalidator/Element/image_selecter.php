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
 * AJAX-based tree search and image selecter.
 * @author Hans De Bisschop
 */
class HTML_QuickForm_image_selecter extends HTML_QuickForm_group
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 365;
    
    private static $initialized;
    
    private $search_url;
    
    private $locale;
    
    private $default_collapsed;
    
    private $height;
    private $width;
    
    private $exclude;
    
    private $defaults;

    function HTML_QuickForm_image_selecter($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default = array (), $options = array())
    {
        parent :: __construct($elementName, $elementLabel);
        $this->_type = 'image_selecter';
        $this->_persistantFreeze = true;
        $this->_appendName = false;
        $this->locale = $locale;
        $this->exclude = array();
        $this->height = self :: DEFAULT_HEIGHT;
        $this->width = self :: DEFAULT_WIDTH;
        $this->search_url = $search_url;
        $this->options = $options;
        $this->build_elements();
        $this->setValue($default);
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
        $this->_elements = array();
        $this->_elements[] = new HTML_QuickForm_hidden($this->getName());
        $this->_elements[] = new HTML_QuickForm_text($this->getName() . '_search', null, array('class' => 'element_query', 'id' => $this->getName() . '_search_field'));
    }

    function getValue()
    {
        return $this->_elements[0]->getValue();
    }

    function exportValue($submitValues, $assoc = false)
    {
        if ($assoc)
        {
            return array($this->getName() => $this->getValue());
        }
        return $this->getValue();
    }

    function setValue($value)
    {
        $this->_elements[0]->setValue($value);
    }

    function toHTML()
    {
        /*
		 * 0 hidden
		 * 1 search
		 */
        $html = array();
        $html[] = '<div id="image_select" style="display: none;">';
        $html[] = '<div id="uploadify"></div>';
        
        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $this->getName() . '_expand_button" class="normal select">' . htmlentities($this->locale['Display']) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->getName() . '_expand_button" style="display: none" class="normal select">' . htmlentities($this->locale['Display']) . '</button>';
        }
        
        $id = 'tbl_' . $this->getName();
        
        $html[] = '<div class="element_finder" id="' . $id . '" style="margin-top: 5px;' . ($this->isCollapsed() ? ' display: none;' : '') . '">';

        // Search
        $html[] = '<div class="element_finder_search">';
        
        $this->_elements[1]->setValue('');
        $html[] = $this->_elements[1]->toHTML();
        
        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $this->getName() . '_collapse_button" style="display: none" class="normal hide">' . htmlentities(Translation :: get('Hide')) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->getName() . '_collapse_button" class="normal hide mini">' . htmlentities(Translation :: get('Hide')) . '</button>';
        }
        
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
        
        // Make sure the elements are all within the div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        // Make sure everything is within the general div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = $this->_elements[0]->toHTML();
        
        $object_id = $this->getValue();
        $is_object_set = ! empty($object_id);
        
        $html[] = '<div id="image_container" ' . ($is_object_set ? '' : ' style="display: none;"') . '>';
        
        if ($is_object_set)
        {
            $image_object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            
            $dimensions = getimagesize($image_object->get_full_path());
            $dimensions = ImageManipulation :: rescale($dimensions[ImageManipulation :: DIMENSION_WIDTH], $dimensions[ImageManipulation :: DIMENSION_HEIGHT], 500, 450, ImageManipulation :: SCALE_INSIDE);
            
            $html[] = '<img id="selected_image" style="width: ' . $dimensions[ImageManipulation :: DIMENSION_WIDTH] . 'px; height: ' . $dimensions[ImageManipulation :: DIMENSION_HEIGHT] . 'px;" src="' . $image_object->get_url() . '" />';
        }
        else
        {
            $html[] = '<img id="selected_image" />';
        
        }
        
        $html[] = '<div class="clear"></div>';
        $html[] = '<button id="change_image" class="negative delete">' . htmlentities(Translation :: get('SelectAnotherImage')) . '</button>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.imageselecter.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify2/swfobject.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify2/jquery.uploadify.v2.1.0.min.js');
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
        
        $html[] = '$("#' . $id . '").elementselecter({ name: "' . $this->getName() . '", search: "' . $this->search_url . '"' . $load_elements . $default_query . ' });';
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