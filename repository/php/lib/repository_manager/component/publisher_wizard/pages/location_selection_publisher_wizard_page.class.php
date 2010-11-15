<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\Application;
use common\libraries\WebApplication;
use common\libraries\AttachmentSupport;
use common\libraries\Path;
use admin\AdminDataManager;
use admin\AdminManager;

/**
 * $Id: location_selection_publisher_wizard_page.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.publication_wizard.pages
 */
require_once dirname(__FILE__) . '/publisher_wizard_page.class.php';
/**
 * Class for application settings page
 * Displays a form where the user can enter the installation settings
 * regarding the applications
 */
class LocationSelectionPublisherWizardPage extends PublisherWizardPage
{
    private $content_objects;
    private $type;
    private $apps;

    public function LocationSelectionPublisherWizardPage($name, $parent)
    {
        parent :: PublisherWizardPage($name, $parent);
        $ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);

        if (empty($ids))
        {
            Request :: set_get('message', Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES));
            $this->get_parent()->display_header($trail, false, true, 'repository publication wizard');
            $this->get_parent()->display_footer();
            exit();
        }

        if (! is_array($ids))
            $ids = array($ids);

        foreach ($ids as $id)
        {
            $lo = $this->get_parent()->retrieve_content_object($id);
            $this->content_objects[] = $lo;
            if ($this->type == null)
                $this->type = $lo->get_type();
            else
            {
                if ($this->type != $lo->get_type())
                {
                    Request :: set_get('message', Translation :: get('ObjectsNotSameType'));
                    $this->get_parent()->display_header($trail, false, true, 'repository publication wizard');
                    $this->get_parent()->display_footer();
                    exit();
                }
            }
        }
    }

    function get_title()
    {
        return Translation :: get('LocationSelection');
    }

    function get_info()
    {
        return Translation :: get('LocationSelectionInfo') . '<br /><br />'; //$this->display_content_objects();//' <b>' . $content_object->get_type() . ' - ' . $content_object->get_title() . '</b>';
    }

    /*function display_content_objects()
	{
		$html = array();
		foreach ($this->content_objects as $lo)
			$html[] = $this->display_content_object($lo);

		return implode("\n", $html);
	}

	function display_content_object($content_object)
	{
		$html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($content_object->get_type())) . 'logo/' . $content_object->get_icon_name() . '.png);">';
		$html[] = '<div class="title">';
		$html[] = $content_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $content_object->get_description();
		$html[] = $this->render_attachments($content_object);
		$html[] = '</div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

	function render_attachments($content_object)
	{
		if ($content_object instanceof AttachmentSupport)
		{
			$attachments = $content_object->get_attached_content_objects();
			if(count($attachments)>0)
			{
				$html[] = '<ul class="attachments_list">';
				Utilities :: order_content_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$disp = ContentObjectDisplay :: factory($attachment);
					$html[] = '<li><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(ContentObject :: type_to_class('TypeName', null, ContentObject :: get_content_object_type_namespace($attachment->get_type()))).'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
		return '';
	}*/

    function buildForm()
    {
        $this->_formBuilt = true;

        $html = '<script type="text/javascript">
							/* <![CDATA[ */
							function setCheckbox(app_name, value) {
								var d = document[\'page_locations\'];
								for (i = 0; i < d.elements.length; i++) {
									if (d.elements[i].type == "checkbox")
									{
									     if(app_name.length == null || d.elements[i].name.substr(0, app_name.length) == app_name)
									     		d.elements[i].checked = value;
									}
								}
							}
							/* ]]> */
							</script>';
        $this->addElement('html', $html);

        $applications = WebApplication :: load_all_from_filesystem(true, true);

        $this->apps = array();

        $location_count = 0;

        $adm = AdminDataManager :: get_instance();

        foreach ($applications as $application_name)
        {
            $application = Application :: factory($application_name);
            $locations = $application->get_content_object_publication_locations($this->content_objects[0], $this->get_parent()->get_user());
            $location_count += count($locations);

            $this->add_locations($application, $application_name, $locations);
        }

        $admin = new AdminManager();
        $locations = $admin->get_content_object_publication_locations($this->content_objects[0], $this->get_parent()->get_user());
        $location_count += count($locations);
        $this->add_locations($admin, 'admin', $locations);

        if ($location_count > 0)
        {
            $this->addElement('html', '<br /><br />');
            //$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES));
            $prevnext[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES) . ' >>', 'style=\'margin-left: -20%;\' class="positive finish"');
            $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        }
        else
        {
            $this->addElement('html', '<div class="warning-message">' . Translation :: get('NoLocationsFound') . '</div>');
        }

        if (count($this->apps) > 1)
        {
            $this->addElement('html', '<br /><br /><a href="?" style="margin-left: 0%"  onclick="setCheckbox(\'\', true); return false;">' . Translation :: get('SelectAll', null, Utilities :: COMMON_LIBRARIES) . '</a>');
            $this->addElement('html', ' - <a href="?" onclick="setCheckbox(\'\', false); return false;">' . Translation :: get('UnSelectAll', null, Utilities :: COMMON_LIBRARIES) . '</a>');
        }

        $this->addElement('html', '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'libraries/resources/javascript/home_ajax.js' . '"></script>');

        $this->setDefaultAction('next');

     //$this->setDefaults($appDefaults);
    }

    function add_locations($application, $application_name, $locations)
    {
        if (count($locations) == 0)
        {
            return;
        }

        $this->apps[] = $application;

        $this->addElement('html', '<div class="block" id="block_introduction" style="background-image: url(' . Theme :: get_image_path('home') . 'block_' . $application_name . '.png);">');
        $this->addElement('html', '<div class="title"><div style="float:left;">' . Translation :: get('ApplicationName', null, Application :: determine_namespace($application_name)));
        $this->addElement('html', '</div><div style="float:right;"><a href="#" class="closeEl"><img class="visible" src="' . Theme :: get_common_image_path() . 'action_visible.png" /><img class="invisible" style="display: none;") src="' . Theme :: get_common_image_path() . 'action_invisible.png" /></a></div><div class="clear">&nbsp;</div></div>');
        $this->addElement('html', '<div class="description"><br />');

        /*$application_name = Utilities :: underscores_to_camelcase($application_name);
        $application_name = strtolower($application_name);*/
        $application->add_publication_attributes_elements($this);

        foreach ($locations as $id => $location)
        {
            $cbname = $application_name . '|' . $id;
            $this->addElement('checkbox', $cbname, '', $location, array('style' => 'margin-left: 12px;'));
            $appDefaults[$cbname] = '1';
        }

        if (count($locations) > 1)
        {
            $this->addElement('html', '<br /><br /><a href="?" style="margin-left: 0%" onclick="setCheckbox(\'' . $application_name . '\', true); return false;">' . Translation :: get('SelectAll', null, Utilities :: COMMON_LIBRARIES) . '</a>');
            $this->addElement('html', ' - <a href="?" onclick="setCheckbox(\'' . $application_name . '\', false); return false;">' . Translation :: get('UnSelectAll', null, Utilities :: COMMON_LIBRARIES) . '</a>');
        }
        $this->addElement('html', '<div style="clear: both;"></div></div></div><br />');
    }
}
?>