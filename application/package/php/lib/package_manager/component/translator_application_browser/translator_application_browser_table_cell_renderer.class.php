<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use user\UserDataManager;
use user\User;
use common\libraries\Utilities;
/**
 * @package package.tables.translator_application_table
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translator_application_browser/translator_application_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'tables/translator_application_table/default_translator_application_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class TranslatorApplicationBrowserTableCellRenderer extends DefaultTranslatorApplicationTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function __construct($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $translator_application)
	{
		if ($column === TranslatorApplicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($translator_application);
		}

		switch($column->get_name())
		{
			case User :: PROPERTY_FIRSTNAME:
				$user = UserDataManager :: get_instance()->retrieve_user($translator_application->get_user_id());
				if($user)
					return $user->get_fullname();
				return Translation :: get('UserUnknown', null, 'user');
			case User :: PROPERTY_USERNAME:
				$user = UserDataManager :: get_instance()->retrieve_user($translator_application->get_user_id());
				if($user)
					return $user->get_username();
				return Translation :: get('UserUnknown', null, 'user');
			case PackageLanguage :: PROPERTY_ENGLISH_NAME:
				$alias = PackageDataManager :: get_instance()->get_alias(PackageLanguage :: get_table_name());
				if($column->get_storage_unit_alias() == $alias)
				{
					$package_language = PackageDataManager :: get_instance()->retrieve_package_language($translator_application->get_source_language_id());
				}
				else
				{
					$package_language = PackageDataManager :: get_instance()->retrieve_package_language($translator_application->get_destination_language_id());
				}

				return $package_language->get_english_name();
		}

		return parent :: render_cell($column, $translator_application);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($translator_application)
	{
		$toolbar = new Toolbar();

		$status = $translator_application->get_status();

		if ($status == TranslatorApplication :: STATUS_ACCEPTED)
		{
			$toolbar->add_item(new ToolbarItem(
    				Translation :: get('Deactivate', null, Utilities :: COMMON_LIBRARIES),
    				Theme :: get_common_image_path() . 'action_deactivate.png', 
    				$this->browser->get_deactivate_translator_application_url($translator_application), 
    				ToolbarItem :: DISPLAY_ICON
    				));
		}
		elseif ($status == TranslatorApplication :: STATUS_PENDING)
		{
			$toolbar->add_item(new ToolbarItem(
    				Translation :: get('Activate', null, Utilities :: COMMON_LIBRARIES),
    				Theme :: get_common_image_path() . 'action_activate.png', 
    				$this->browser->get_activate_translator_application_url($translator_application), 
    				ToolbarItem :: DISPLAY_ICON
    				));
		}
		
		$toolbar->add_item(new ToolbarItem(
    				Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
    				Theme :: get_common_image_path() . 'action_delete.png', 
    				$this->browser->get_delete_translator_application_url($translator_application), 
    				ToolbarItem :: DISPLAY_ICON
    				));

		return $toolbar->as_html();
	}
}
?>