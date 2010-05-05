<?php
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_data_provider.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_item.class.php';

require_once Path :: get_application_path() . '/lib/gradebook/gradebook_data_manager.class.php';

class WeblcmsGradebookTreeMenuDataProvider extends TreeMenuDataProvider
{
	const PARAM_ID = 'category_id';

	public function get_tree_menu_data()
	{
		
	}
    
    public function get_id_param()
    {
    	return self :: PARAM_ID;
    }
}
?>