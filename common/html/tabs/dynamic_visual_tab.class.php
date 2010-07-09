<?php
class DynamicVisualTab extends DynamicTab
{
    private $content;
    private $selected;

    /**
     * @param integer $id
     * @param string $name
     * @param string $image
     * @param string $link
     * @param boolean $selected
     */
    public function DynamicVisualTab($id, $name, $image, $link, $selected = false)
    {
        parent :: __construct($id, $name, $image);
        $this->link = $link;
        $this->selected = $selected;
    }

    /**
     * @return the $link
     */
    public function get_link()
    {
        return $this->link;
    }

    /**
     * @param $link the link to set
     */
    public function set_link($link)
    {
        $this->link = $link;
    }

    /**
     * @return the $selected
     */
    public function get_selected()
    {
        return $this->selected;
    }

    /**
     * @param $selected the selected to set
     */
    public function set_selected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * @param string $tab_name
     * @return string
     */
    public function header()
    {
        if ($this->get_selected() == true)
        {
            $classes = 'ui-state-default ui-corner-top ui-state-active ui-tabs-selected dynamic_visual_tab';
        }
        else
        {
            $classes = 'ui-state-default ui-corner-top';
        }

        $html = array();
        $html[] = '<li class="' . $classes . '"><a href="' . $this->get_link() . '">';
        $html[] = '<span class="category">';
        if ($this->get_image())
        {
            $html[] = '<img src="' . $this->get_image() . '" border="0" style="vertical-align: middle;" alt="' . $this->get_name() . '" title="' . $this->get_name() . '"/>';
        }
        $html[] = '<span class="title">' . $this->get_name() . '</span>';
        $html[] = '</span>';
        $html[] = '</a></li>';
        return implode("\n", $html);
    }

    /**
     * @param string $tab_name
     * @return string
     */
    public function body()
    {
        return null;
    }
}