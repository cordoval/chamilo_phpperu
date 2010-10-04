<?php
/**
 * $Id: navigation_item_form.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib
 */

class NavigationItemForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $menuitem;

    function NavigationItemForm($form_type, $menuitem, $action)
    {
        parent :: __construct('navigation_item', 'post', $action);
        
        $this->menuitem = $menuitem;
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Main') . '</span>');
        $this->addElement('text', NavigationItem :: PROPERTY_TITLE, Translation :: get('NavigationItemTitle'), array("size" => "50"));
        $this->addRule(NavigationItem :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('select', NavigationItem :: PROPERTY_CATEGORY, Translation :: get('NavigationItemParent'), $this->get_categories());
        $this->addRule(NavigationItem :: PROPERTY_CATEGORY, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Link') . '</span>');
        
        $choices[] = $this->createElement('radio', 'app', '', Translation :: get('Application'), 0, array('onclick' => 'javascript:application_clicked()'));
        $choices[] = $this->createElement('radio', 'app', '', Translation :: get('ExternalLink'), 1, array('onclick' => 'javascript:external_link_clicked()'));
        $this->addGroup($choices, null, Translation :: get('applink'), '<br />', false);
        
        $this->addElement('html', '<div style="margin-left:25px;display:block;" id="application">');
        $this->addElement('select', NavigationItem :: PROPERTY_APPLICATION, Translation :: get('NavigationItemApplication'), $this->get_applications());
        $this->addElement('text', NavigationItem :: PROPERTY_EXTRA, Translation :: get('NavigationItemExtra'), array("size" => "50"));
        $this->addElement('html', '</div>');
        
        $this->addElement('html', '<div style="margin-left:25px;display:block;" id="external_link">');
        $this->addElement('text', NavigationItem :: PROPERTY_URL, Translation :: get('Url'), array("size" => "50"));
        $this->addElement('html', '</div>');
        
        $hidden = 'external_link';
        
        if ($this->form_type == self :: TYPE_EDIT && $this->menuitem && $this->menuitem->get_application() == '')
        {
            $hidden = 'application';
        }
        
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					document.getElementById('" . $hidden . "').style.display='none';
					function application_clicked() {
						document.getElementById('application').style.display='';
						document.getElementById('external_link').style.display='none';
					}
					function external_link_clicked() {
						document.getElementById('external_link').style.display='';
						document.getElementById('application').style.display='none';
					}
					/* ]]> */
					</script>\n");
        
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
    //$this->addElement('submit', 'navigation_item', Translation :: get('Ok'));
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', NavigationItem :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_navigation_item()
    {
        $menuitem = $this->menuitem;
        $values = $this->exportValues();
        
        $menuitem->set_title($values[NavigationItem :: PROPERTY_TITLE]);
        
        if ($values['app'] == 0)
        {
            $menuitem->set_application($values[NavigationItem :: PROPERTY_APPLICATION]);
            $menuitem->set_section($values[NavigationItem :: PROPERTY_APPLICATION]);
            $menuitem->set_url(' ');
        }
        else
        {
            $url = $values[NavigationItem :: PROPERTY_URL];
            if (! $url || $url == '')
                $url = 'http://www.chamilo.org';
            if (substr($url, 0, 7) != 'http://')
                $url = 'http://' . $url;
            
            $menuitem->set_url($url);
            $menuitem->set_application('');
            $menuitem->set_section('');
        }
        
        $menuitem->set_extra($values[NavigationItem :: PROPERTY_EXTRA]);
        $menuitem->set_category($values[NavigationItem :: PROPERTY_CATEGORY]);
        $menuitem->set_is_category(0);
        
        return $menuitem->update();
    }

    function create_navigation_item()
    {
        $menuitem = $this->menuitem;
        $values = $this->exportValues();
        
        $menuitem->set_title($values[NavigationItem :: PROPERTY_TITLE]);
        
        if ($values['app'] == 0)
        {
            $menuitem->set_application($values[NavigationItem :: PROPERTY_APPLICATION]);
            $menuitem->set_section($values[NavigationItem :: PROPERTY_APPLICATION]);
            $menuitem->set_url(' ');
        }
        else
        {
            $url = $values[NavigationItem :: PROPERTY_URL];
            if (! $url || $url == '')
                $url = 'http://www.chamilo.org';
            if (substr($url, 0, 7) != 'http://')
                $url = 'http://' . $url;
            
            $menuitem->set_url($url);
            $menuitem->set_application('');
            $menuitem->set_section('');
        }
        
        $menuitem->set_category($values[NavigationItem :: PROPERTY_CATEGORY]);
        $menuitem->set_extra($values[NavigationItem :: PROPERTY_EXTRA]);
        $menuitem->set_is_category(0);
        
        return $menuitem->create();
    }

    function get_categories()
    {
        $conditions[] = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, 0);
        $conditions[] = new EqualityCondition(NavigationItem :: PROPERTY_IS_CATEGORY, 1);
        $condition = new AndCondition($conditions);
        
        $items = MenuDataManager :: get_instance()->retrieve_navigation_items($condition, null, null, new ObjectTableOrder(NavigationItem :: PROPERTY_SORT));
        $item_options = array();
        $item_options[0] = Translation :: get('Root');
        
        while ($item = $items->next_result())
        {
            $item_options[$item->get_id()] = '-- ' . $item->get_title();
        }
        return $item_options;
    }

    function get_applications()
    {
        $items = WebApplication :: load_all(false);
        $applications = array();
        $applications['root'] = Translation :: get('Root');
        
        foreach ($items as $item)
        {
            $applications[$item] = Translation :: get(Application :: application_to_class($item));
        }
        return $applications;
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $menuitem = $this->menuitem;
        $defaults[NavigationItem :: PROPERTY_TITLE] = $menuitem->get_title();
        $defaults[NavigationItem :: PROPERTY_CATEGORY] = $menuitem->get_category();
        if ($this->form_type == self :: TYPE_EDIT)
            $defaults['app'] = ($menuitem->get_application() != '') ? 0 : 1;
        else
            $defaults['app'] = 0;
        $defaults[NavigationItem :: PROPERTY_APPLICATION] = $menuitem->get_application();
        $defaults[NavigationItem :: PROPERTY_URL] = $menuitem->get_url();
        $defaults[NavigationItem :: PROPERTY_EXTRA] = $menuitem->get_extra();
        parent :: setDefaults($defaults);
    }

    function get_navigation_item()
    {
        return $this->menuitem;
    }
}
?>