<?php
class DynamicContentTab extends DynamicTab
{
    private $content;

    /**
     * @param integer $id
     * @param string $name
     * @param string $image
     * @param string $content
     */
    public function DynamicContentTab($id, $name, $image, $content)
    {
        parent :: __construct($id, $name, $image);
        $this->content = $content;
    }

    /**
     * @return the $content
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * @param $content the $content to set
     */
    public function set_content($content)
    {
        $this->content = $content;
    }

    /**
     * @param string $tab_name
     * @return string
     */
    public function body($tab_name)
    {
        $html = array();
        $html[] = $this->body_header($tab_name);
        $html[] = $this->content;
        $html[] = $this->body_footer($tab_name);
        return implode("\n", $html);
    }
}