<?php
/**
 * $Id: toolbar_item.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.toolbar
 */
class ToolbarItem
{
    const DISPLAY_ICON = 1;
    const DISPLAY_LABEL = 2;
    const DISPLAY_ICON_AND_LABEL = 3;

    private $label;
    private $display;
    private $image;
    private $href;
    private $confirmation;
    private $class;
    private $target;

    function ToolbarItem($label = null, $image = null, $href = null, $display = self :: DISPLAY_ICON_AND_LABEL, $confirmation = false, $class = null, $target = null)
    {
        $this->label = $label;
        $this->display = $display;
        $this->image = $image;
        $this->href = $href;
        $this->confirmation = $confirmation;
        $this->class = $class;
        $this->target = $target;
    }

    function get_label()
    {
        return $this->label;
    }
    
    function set_label($label)
    {
    	$this->label = $label;
    }

    function get_display()
    {
        return $this->display;
    }
    
	function set_display($display)
    {
    	$this->display = $display;
    }

    function get_image()
    {
        return $this->image;
    }
    
	function set_image($image)
    {
    	$this->image = $image;
    }

    function get_href()
    {
        return $this->href;
    }
    
	function set_href($href)
    {
    	$this->href = $href;
    }

    function get_target()
    {
        return $this->target;
    }
    
	function set_target($target)
    {
    	$this->target = $target;
    }

    function get_confirmation()
    {
        return $this->confirmation;
    }
    
	function set_confirmation($confirmation)
    {
    	$this->confirmation = $confirmation;
    }

    function needs_confirmation()
    {
        return $this->confirmation;
    }

    function as_html()
    {
        $label = ($this->get_label() ? htmlspecialchars($this->get_label()) : null);
        if (! $this->get_display())
        {
            $this->display = self :: DISPLAY_ICON;
        }
        $display_label = ($this->display & self :: DISPLAY_LABEL) == self :: DISPLAY_LABEL && ! empty($label);

        $button = '';
        if (($this->display & self :: DISPLAY_ICON) == self :: DISPLAY_ICON && isset($this->image))
        {
            $button .= '<img src="' . htmlentities($this->image) . '" alt="' . $label . '" title="' . $label . '"' . ($display_label ? ' class="labeled"' : '') . '/>';
        }

        if ($this->class)
        {
            $class = ' class="' . $this->class . '"';
        }
        else
        {
            $class = '';
        }

        if ($display_label)
        {
            if ($this->get_href())
            {
                $button .= '<span>' . $label . '</span>';
            }
            else
            {
                $button .= '<span'. $class .'>' . $label . '</span>';
            }
        }

        if ($this->get_href())
        {

            if ($this->target)
            {
                $target = ' target="' . $this->target . '"';
                $button = '<a' . $class . $target . ' href="' . htmlentities($this->href) . '" title="' . $label . '"' . ($this->needs_confirmation() ? ' onclick="return confirm(\'' . addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))) . '\');"' : '') . '>' . $button . '</a>';
            }
            else
            {
                $button = '<a' . $class . ' href="' . htmlentities($this->href) . '" title="' . $label . '"' . ($this->needs_confirmation() ? ' onclick="return confirm(\'' . addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))) . '\');"' : '') . '>' . $button . '</a>';
            }
        }

        return $button;
    }
}
?>