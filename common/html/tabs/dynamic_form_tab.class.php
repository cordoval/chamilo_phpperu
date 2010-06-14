<?php
class DynamicFormTab extends DynamicTab
{
    private $method;

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

    public function body($tab_name)
    {

    }

    /**
     * @param string $tab_name
     * @return string
     */
    public function body_form($tab_name, $form)
    {
        //        $html = array();
        $form->addElement('html', $this->body_header($tab_name));
                call_user_func(array($form, $this->get_method()));
//        $form->addElement('html', 'Body');
        $form->addElement('html', $this->body_footer($tab_name));
        //        return implode("\n", $html);
    }
}