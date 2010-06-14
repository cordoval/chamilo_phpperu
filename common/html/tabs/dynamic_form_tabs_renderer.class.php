<?php
class DynamicFormTabsRenderer extends DynamicTabsRenderer
{
    private $form;

    public function DynamicFormTabsRenderer($name, $form)
    {
        parent :: __construct($name);
        $this->form = $form;
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

    public function render()
    {
        $this->form->addElement('html', $this->header());

        foreach ($this->get_tabs() as $key => $tab)
        {
            $tab->set_form($this->form);
            $tab->body($this->get_name() . '_' . $key);
        }

        $this->form->addElement('html', $this->footer());
    }

}