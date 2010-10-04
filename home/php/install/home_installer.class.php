<?php
/**
 * $Id: home_installer.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.install
 */
/**
 *	This installer can be used to create the storage structure for the
 * home application.
 */
class HomeInstaller extends Installer
{

    /**
     * Constructor
     */
    function HomeInstaller($values)
    {
        parent :: __construct($values, HomeDataManager :: get_instance());
    }

    /**
     * Runs the install-script.
     * @todo This function now uses the function of the RepositoryInstaller
     * class. These shared functions should be available in a common base class.
     */
    function install_extra()
    {
        if (! $this->create_basic_home())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('HomeCreated'));
        }
        
        return true;
    }

    function create_basic_home()
    {
        // First test-tab
        $tab = new HomeTab();
        $tab->set_title(Translation :: get('Home'));
        $tab->set_user('0');
        if (! $tab->create())
        {
            return false;
        }
        
        $row = new HomeRow();
        $row->set_title(Translation :: get('Site'));
        $row->set_tab($tab->get_id());
        $row->set_user('0');
        if (! $row->create())
        {
            return false;
        }
        
        $column_news = new HomeColumn();
        $column_news->set_row($row->get_id());
        $column_news->set_title(Translation :: get('News'));
        $column_news->set_sort('1');
        $column_news->set_width('66');
        $column_news->set_user('0');
        if (! $column_news->create())
        {
            return false;
        }
        
        $block_test = new HomeBlock();
        $block_test->set_column($column_news->get_id());
        $block_test->set_title(Translation :: get('SystemAnnouncements'));
        $block_test->set_application('admin');
        $block_test->set_component('system_announcements');
        $block_test->set_user('0');
        if (! $block_test->create())
        {
            return false;
        }
        
        $column_varia = new HomeColumn();
        $column_varia->set_row($row->get_id());
        $column_varia->set_title(Translation :: get('Various'));
        $column_varia->set_sort('2');
        $column_varia->set_width('33');
        $column_varia->set_user('0');
        if (! $column_varia->create())
        {
            return false;
        }
        
        $block_user = new HomeBlock();
        $block_user->set_column($column_varia->get_id());
        $block_user->set_title(Translation :: get('User'));
        $block_user->set_application('user');
        $block_user->set_component('login');
        $block_user->set_user('0');
        if (! $block_user->create())
        {
            return false;
        }
        
        $block_portal_home = new HomeBlock();
        $block_portal_home->set_column($column_news->get_id());
        $block_portal_home->set_title(Translation :: get('PortalHome'));
        $block_portal_home->set_application('admin');
        $block_portal_home->set_component('portal_home');
        $block_portal_home->set_user('0');
        if (! $block_portal_home->create())
        {
            return false;
        }
        
        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>