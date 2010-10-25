<?php
namespace common\libraries;
class DynamicFormTab extends DynamicTab
{
    private $method;
    private $form;

    /**
     * @param integer $id
     * @param string $name
     * @param string $image
     * @param string $form
     * @param string $method
     */
    public function DynamicFormTab($id, $name, $image, $method)
    {
        parent :: __construct($id, $name, $image);
        $this->method = $method;
    }

    /**
     * @return the $method
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     * @param $method the $method to set
     */
    public function set_method($method)
    {
        $this->method = $method;
    }

    /**
     * @return the $form
     */
    public function get_form()
    {
        return $this->form;
    }

    /**
     * @param $form the $form to set
     */
    public function set_form($form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function body()
    {
        $this->get_form()->addElement('html', $this->body_header());
        call_user_func(array($this->get_form(), $this->get_method()));
        $this->get_form()->addElement('html', $this->body_footer());
    }

    public function get_link() 
	{
		return "#" . $this->get_id();
	}

}