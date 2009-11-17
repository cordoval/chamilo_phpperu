<?php
/**
 * $Id: trackers_selection_archive_wizard_page.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component.wizards.archive
 */
require_once dirname(__FILE__) . '/archive_wizard_page.class.php';
/**
 * Page in the archive wizard in which some config settings are asked to the
 * user.
 */
class TrackersSelectionArchiveWizardPage extends ArchiveWizardPage
{

    /**
     * Returns the title of this page
     * @return string the title
     */
    function get_title()
    {
        return Translation :: get('ArchiveTrackersSelectionTitle');
    }

    /**
     * Returns the info of this page
     * @return string the info
     */
    function get_info()
    {
        return Translation :: get('ArchiveTrackersSelectionInfo');
    }

    /**
     * Builds the form that must be visible on this page
     */
    function buildForm()
    {
        $this->_formBuilt = true;
        $defaults = array();
        
        $events = $this->get_parent()->retrieve_events();
        $previousblock = '';
        
        $this->addElement('html', '<div style="margin-top: 10px;">&nbsp;</div>');
        $this->addElement('html', '<div id="selectall" style="color: #4171B5; font-weight: bold; cursor: pointer; float: left;">' . Translation :: get('SelectAll') . '</div> &nbsp; | &nbsp; ');
        $this->addElement('html', '<div id="unselectall" style="color: #4171B5; font-weight: bold; cursor: pointer; display: inline;">' . Translation :: get('UnSelectAll') . '</div>');
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/checkboxes.js'));
        
        while ($event = $events->next_result())
        {
            if ($event->get_block() != $previousblock)
            {
                $message = '<div style="float:left;"><img src="' . Theme :: get_image_path('admin') . 'place_' . $event->get_block() . '.png" alt="' . $event->get_block() . '"></div>';
                $previousblock = $event->get_block();
            }
            else
                $message = "";
            
            $this->addElement('checkbox', $event->get_block() . '_' . $event->get_name() . '_event', $message, $event->get_name(), 'onclick=\'event_clicked("' . $event->get_block() . '_' . $event->get_name() . '_event", this.form)\' style=\'margin-top: 20px;\' class="chckbox"');
            $defaults[$event->get_name() . 'event'] = 1;
            
            $trackers = $this->get_parent()->retrieve_trackers_from_event($event->get_id());
            
            foreach ($trackers as $tracker)
            {
                $this->addElement('checkbox', $event->get_block() . '_' . $event->get_name() . '_event_' . $tracker->get_id(), '', $tracker->get_class(), 'onclick=\'tracker_clicked("' . $event->get_block() . '_' . $event->get_name() . '_event", this)\' style=\'margin-left: 20px;\' class="chckbox"');
                $defaults[$event->get_name() . 'event' . $tracker->get_id()] = 1;
            }
        }
        
        $this->add_js_functions();
        $this->setDefaults($defaults);
        
        //$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous'), 'style=\'margin-top: 20px;\'');
        //$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>', 'style=\'margin-top: 20px;\'');
        $prevnext[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next') . ' >>', array('class' => 'next'));
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        
        $this->setDefaultAction('next');
    }

    /**
     * Adds javascript functions for trackers
     */
    function add_js_functions()
    {
        $html = array();
        $html[] = '<script language="JavaScript" type="text/javascript">';
        $html[] = 'function tracker_clicked(event_name, object)';
        $html[] = '{';
        $html[] = '  var d = object.form[event_name];';
        $html[] = '  if(d.checked == false && object.checked == true)';
        $html[] = '  {';
        $html[] = '    d.checked = true;';
        $html[] = '  }';
        $html[] = '  ';
        $html[] = '}';
        $html[] = '';
        $html[] = 'function event_clicked(event_name, form)';
        $html[] = '{';
        $html[] = '  if(form[event_name].checked == true) return true';
        $html[] = '';
        $html[] = '  for (i = 0; i < form.elements.length; i++)';
        $html[] = '  {';
        $html[] = '    if (form.elements[i].type == "checkbox" && form.elements[i].name.substr(0,event_name.length) == event_name)';
        $html[] = '    {';
        $html[] = '        form.elements[i].checked = false;';
        $html[] = '    }';
        $html[] = '  }';
        $html[] = '}';
        $html[] = '</script>';
        
        $this->addElement('html', implode("\n", $html));
    }
}
?>