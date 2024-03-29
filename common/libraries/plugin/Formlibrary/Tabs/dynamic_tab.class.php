<?php
abstract class DynamicTab
{
    private $id;
    private $name;
    private $image;

    /**
     * @param integer $id
     * @param string $name
     * @param string $image
     */
    public function DynamicTab($id, $name, $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
    }

    /**
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @return the $name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @param $name the $name to set
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @return the $image
     */
    public function get_image()
    {
        return $this->image;
    }

    /**
     * @param $image the $image to set
     */
    public function set_image($image)
    {
        $this->image = $image;
    }

    abstract public function get_link();

    /**
     * @param string $tab_name
     * @return string
     */
    public function header()
    {
        $html = array();
        $html[] = '<li><a href="' . $this->get_link() . '">';
        $html[] = '<span class="category">';
        if ($this->image)
        {
            $html[] = '<img src="' . $this->image . '" border="0" style="vertical-align: middle;" alt="' . $this->name . '" title="' . $this->name . '"/>';
        }
        $html[] = '<span class="title">' . $this->name . '</span>';
        $html[] = '</span>';
        $html[] = '</a></li>';
        return implode("\n", $html);
    }

    /**
     * @return string
     */
    protected function body_header()
    {
        $html = array();
        $html[] = '<h2>';
        if ($this->get_image())
        {
            $html[] = '<img src="' . $this->get_image() . '" border="0" style="vertical-align: middle;" alt="' . $this->get_name() . '" title="' . $this->get_name() . '"/>&nbsp;';
        }
        $html[] = $this->get_name();
        $html[] = '</h2>';

        $html[] = '<div class="admin_tab" id="' . $this->get_id() . '">';
        $html[] = '<a class="prev"></a>';
        return implode("\n", $html);
    }

    /**
     * @return string
     */
    protected function body_footer()
    {
        $html = array();
        $html[] = '<a class="next"></a>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }

    /**
     * @param string $tab_name
     * @return string
     */
    abstract function body();
}