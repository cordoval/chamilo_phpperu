<?php
/**
 * $Id: home_block.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib
 */
require_once 'XML/Unserializer.php';

class HomeBlock extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'block';

    const PROPERTY_COLUMN = 'column_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_COMPONENT = 'component';
    const PROPERTY_VISIBILITY = 'visibility';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_COLUMN, self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_APPLICATION, self :: PROPERTY_COMPONENT, self :: PROPERTY_VISIBILITY, self :: PROPERTY_USER));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return HomeDataManager :: get_instance();
    }

    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    function set_sort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }

    function get_column()
    {
        return $this->get_default_property(self :: PROPERTY_COLUMN);
    }

    function set_column($column)
    {
        $this->set_default_property(self :: PROPERTY_COLUMN, $column);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    function get_component()
    {
        return $this->get_default_property(self :: PROPERTY_COMPONENT);
    }

    function set_component($component)
    {
        $this->set_default_property(self :: PROPERTY_COMPONENT, $component);
    }

    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    function set_visibility($visibility)
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
    }

    function set_visible()
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, true);
    }

    function set_invisible()
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, false);
    }

    function is_visible()
    {
        return $this->get_visibility();
    }

    function create()
    {
        $wdm = $this->get_data_manager();

        $condition = new EqualityCondition(self :: PROPERTY_COLUMN, $this->get_column());
        $sort = $wdm->retrieve_max_sort_value(self :: get_table_name(), self :: PROPERTY_SORT, $condition);
        $this->set_sort($sort + 1);

        $success_block = $wdm->create_home_block($this);
        if (! $success_block)
        {
            return false;
        }

        $success_settings = $this->create_initial_settings();
        if (! $success_settings)
        {
            return false;
        }

        return true;
    }

    function create_initial_settings()
    {
        $application = $this->get_application();

        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        $file = $base_path . $application . '/block/' . $application . '_' . $this->get_component() . '.xml';

        $result = array();

        if (file_exists($file))
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('category', 'setting'));

            // userialize the document
            $status = $unserializer->unserialize($file, true);

            if (PEAR :: isError($status))
            {
                echo 'Error: ' . $status->getMessage();
            }
            else
            {
                $data = $unserializer->getUnserializedData();

                $setting_categories = $data['settings']['category'];
                foreach ($setting_categories as $setting_category)
                {
                    foreach ($setting_category['setting'] as $setting)
                    {
                        $block_config = new HomeBlockConfig();
                        $block_config->set_block_id($this->get_id());
                        $block_config->set_variable($setting['name']);
                        $block_config->set_value($setting['default']);

                        if (! $block_config->create())
                        {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    function get_configuration()
    {
        $hdm = HomeDataManager :: get_instance();
        $condition = new EqualityCondition(HomeBlockConfig :: PROPERTY_BLOCK_ID, $this->get_id());
        $configs = $hdm->retrieve_home_block_config($condition);
        $configuration = array();

        while ($config = $configs->next_result())
        {
            $configuration[$config->get_variable()] = $config->get_value();
        }
        return $configuration;
    }

    function is_configurable()
    {
        $application = $this->get_application();

        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        $file = $base_path . $application . '/block/' . $application . '_' . $this->get_component() . '.xml';

        if (file_exists($file))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>