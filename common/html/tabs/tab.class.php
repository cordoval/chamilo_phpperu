<?php
class Tab
{
    const TYPE_CONTENT = 1;
    const TYPE_ACTIONS = 2;
    
    private $name;
    private $image;
    private $type;
    private $content;

    public function Tab($name, $image, $content, $type = self :: TYPE_CONTENT)
    {
        $this->name = $name;
        $this->image = $image;
        $this->content = $content;
        $this->type = $type;
    }

    /**
     * @return the $type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @param $type the $type to set
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    public function get_header($tab_name)
    {
        $html = array();
        $html[] = '<li><a href="#' . $tab_name . '">';
        $html[] = '<span class="category">';
        $html[] = '<img src="' . $this->image . '" border="0" style="vertical-align: middle;" alt="' . $this->name . '" title="' . $this->name . '"/>';
        $html[] = '<span class="title">' . $this->name . '</span>';
        $html[] = '</span>';
        $html[] = '</a></li>';
        return implode("\n", $html);
    }

    public function get_content($tab_name)
    {
        $html = array();
        
        $html[] = '<h2><img src="' . $this->image . '" border="0" style="vertical-align: middle;" alt="' . $this->name . '" title="' . $this->name . '"/>&nbsp;' . $this->name . '</h2>';
        
        $tab_classes = ($this->type == self :: TYPE_CONTENT ? '' : ' no-padding');
        
        $html[] = '<div class="admin_tab' . $tab_classes . '" id="' . $tab_name . '">';
        $html[] = '<a class="prev"></a>';
        
        $html[] = $this->content;
        
        $html[] = '<a class="next"></a>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }
}