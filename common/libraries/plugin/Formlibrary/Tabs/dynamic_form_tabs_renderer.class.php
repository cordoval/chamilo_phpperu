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
        $header = new AddHtml($this->form, $this->body_header());
    	$this->form->add_element($header);
    	//$this->form->addElement('html', $this->header());

        foreach ($this->get_tabs() as $key => $tab)
        {
            $tab->set_form($this->form);
            $tab->body();
        }
		$footer = new AddHtml($this->form, $this->body_header());
    	$this->form->add_element($footer);
        //$this->form->addElement('html', $this->footer());
    }

    public function footer()
    {
        $html = array();
        $html[] = '<script language="javascript">';
        $html[] = ' var element = "' . $this->get_name() . '"';
        $html[] = '</script>';
        $html[] = ResourceManager::get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/dynamic_form_tabs.js');
        $html[] = parent :: footer();
        return implode("\n", $html);
    }

}