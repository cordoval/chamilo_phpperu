<?php
/**
 * $Id: element_finder.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.formvalidator.Element
 */
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Select.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Text.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Hidden.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Button.class.php';

/**
 * AJAX-based tree search and image selecter.
 * @author Hans De Bisschop
 */
class ImageSelecter extends Grouping
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

    public function ImageSelecter($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default = array (), $options = array())
    {
        parent::Grouping($elementName, $elementLabel, false);  
        $this->locale = $locale;
        $this->exclude = array();
        $this->height = self :: DEFAULT_HEIGHT;
        $this->width = self :: DEFAULT_WIDTH;
        $this->search_url = $search_url;
        $this->elements = $options;
        $this->build_elements();
        $this->set_value($default);
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
        $hidden = new Hidden($this->form, $this->get_name());
    	$text = new Text($this->form, $this->get_name() . '_search', null, null);
    	$at = new AttributeClass('element_query');
    	$text->get_attributestorage()->add_attribute($at);
    	$this->elements[] = $hidden;
    	$this->elements[] = $text;    	
    }

    public function get_value()
    {
        if(!empty($_POST[$this->get_name()]))
    		return $_POST[$this->get_name()];
    }

    public function set_value($value)
    {
        $this->elements[0]->set_value($value);
    }

    public function toHTML()
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
            $html[] = '<button id="' . $this->get_name() . '_expand_button" class="normal select">' . htmlentities($this->locale['Display']) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->get_name() . '_expand_button" style="display: none" class="normal select">' . htmlentities($this->locale['Display']) . '</button>';
        }
        
        $id = 'tbl_' . $this->get_name();
        
        $html[] = '<div class="element_finder" id="' . $id . '" style="margin-top: 5px;' . ($this->isCollapsed() ? ' display: none;' : '') . '">';

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
        
        // Make sure the elements are all within the div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        // Make sure everything is within the general div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = $this->_elements[0]->render();
        
        $object_id = $this->get_value();
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
        
        $html[] = 'var ' . $this->get_name() . '_excluded = new Array(' . implode(',', $exclude_ids) . ');';
        
        $load_elements = $this->options['load_elements'];
        $load_elements = (isset($load_elements) && $load_elements == false ? ', loadElements: false' : ', loadElements: true');
        
        $default_query = $this->options['default_query'];
        $default_query = (isset($default_query) && ! empty($default_query) ? ', defaultQuery: "' . $default_query . '"' : '');
        
        $html[] = '$("#' . $id . '").elementselecter({ name: "' . $this->get_name() . '", search: "' . $this->search_url . '"' . $load_elements . $default_query . ' });';
        $html[] = '</script>';
        
        return implode("\n", $html);
    }
}
?>