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
            $tab->body();
        }

        $this->form->addElement('html', $this->footer());
    }

    public function footer()
    {
        $html = array();
        $html[] = ResourceManager::get_instance()->get_resource_html(Path :: get_library_path() . 'javascript/dynamic_form_tabs.js');
        $html[] = parent :: footer();
        return implode("\n", $html);
    }

}