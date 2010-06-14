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

        // Tab content
        $tabs = $this->get_tabs();

        foreach ($tabs as $key => $tab)
        {
            $tab->body_form($this->get_name() . '_' . $key, $this->form);
        }

        $this->form->addElement('html', $this->footer());

//        return implode("\n", $html);
    }

}