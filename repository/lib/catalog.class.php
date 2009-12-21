<?php
/**
 * $Id: catalog.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
class Catalog extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_CATALOG_TYPE = 'type';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_ORDER = 'order';
    
    
    const CATALOG_DAY = 'day';
    const CATALOG_MONTH = 'month';
    const CATALOG_YEAR = 'year';
    const CATALOG_HOUR = 'hour';
    const CATALOG_MIN = 'min';
    const CATALOG_SEC = 'sec';
    const CATALOG_LOM_LANGUAGE = 'lom_language';
    const CATALOG_LOM_ROLE = 'lom_role';
    const CATALOG_LOM_COPYRIGHT = 'lom_copyright';

    /*************************************************************************/
    
    public function Catalog($defaultProperties = array ())
    {
        parent :: __construct($defaultProperties);
    }

	/*************************************************************************/
    
    public function set_parent_id($parent_id)
    {
        if (isset($parent_id) && is_numeric($parent_id) > 0)
        {
            $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
        }
    }

    /**
     * @return string The catalog item parent id value
     */
    public function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }
    
    /*************************************************************************/
    
    public function set_type($type)
    {
        if (isset($type) && strlen($type) > 0)
        {
            $this->set_default_property(self :: PROPERTY_CATALOG_TYPE, $type);
        }
    }

    /**
     * @return string The catalog type
     */
    public function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_CATALOG_TYPE);
    }

    /*************************************************************************/
    
    public function set_title($title)
    {
        if (isset($title) && strlen($title) > 0)
        {
            $this->set_default_property(self :: PROPERTY_TITLE, $title);
        }
    }

    /**
     * @return string The catalog item title to display
     */
    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /*************************************************************************/
    
    public function set_order($order)
    {
        if (isset($order) && is_numeric($order) > 0)
        {
            $this->set_default_property(self :: PROPERTY_ORDER, $order);
        }
    }

    /**
     * @return string The catalog item order value
     */
    public function get_order()
    {
        return $this->get_default_property(self :: PROPERTY_ORDER);
    }

    /*************************************************************************/
    
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_PARENT_ID;
        $extended_property_names[] = self :: PROPERTY_CATALOG_TYPE;
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_ORDER;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    public static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    /*************************************************************************
     * Fat model methods
     *************************************************************************/
    
    /**
     * Get an array of [value] => [display title] pairs that can be used for instance to fill a dropdown list
     * 
     * @param $catalog_type The type of the catalog to retrieve. Use one of the Catalog class constants as value
     * @param $with_empty_value Indicates wether the catalog must contain an empty value at the first position
     * @param $translate_title Indicates wether the display titles must be translated
     * @param $where_value An optional value for the dynamic catalogs that are made from an SQL query with a WHERE clause
     * @return array Array of [value] => [display title] pairs
     */
    public static function get_catalog($catalog_type, $with_empty_value = false, $translate_title = false, $where_value = null)
    {
        $query = null;
        $condition = null;
        $catalog = null;
        
        if ($catalog_type == 'switch_disciplines')
        {
            $query = 'SELECT id, french as title, `order` FROM unige_switch_disciplines';
            
            if (isset($where_value))
            {
                $condition = new EqualityCondition('parent_id', $where_value);
            }
            else
            {
                $condition = new EqualityCondition('parent_id', null);
            }
            
            $order = new ObjectTableOrder('french', SORT_ASC);
        }
        elseif ($catalog_type == Catalog :: CATALOG_LOM_LANGUAGE || $catalog_type == Catalog :: CATALOG_LOM_ROLE || $catalog_type == Catalog :: CATALOG_LOM_COPYRIGHT)
        {
            $query = 'SELECT value as Id, name as title, sort as `order` FROM repository_content_object_metadata_catalog';
            $condition = new EqualityCondition('type', $catalog_type);
            $order = new ObjectTableOrder('sort', SORT_ASC);
        }
        elseif ($catalog_type == self :: CATALOG_DAY)
        {
            $catalog = Catalog :: get_day_catalog();
        }
        elseif ($catalog_type == self :: CATALOG_MONTH)
        {
            $catalog = Catalog :: get_month_catalog();
        }
        elseif ($catalog_type == self :: CATALOG_YEAR)
        {
            $catalog = Catalog :: get_year_catalog();
        }
        elseif ($catalog_type == self :: CATALOG_HOUR)
        {
            $catalog = Catalog :: get_hour_catalog();
        }
        elseif ($catalog_type == self :: CATALOG_MIN)
        {
            $catalog = Catalog :: get_min_catalog();
        }
        elseif ($catalog_type == self :: CATALOG_SEC)
        {
            $catalog = Catalog :: get_sec_catalog();
        }
        
        if (isset($query))
        {
            /*
             * Catalog must be built from datasource content
             */
            
            $catalog = array();
            
            $rdm = RepositoryDataManager :: get_instance();
            $rs = $rdm->retrieve_catalog($query, 'catalog', $condition, null, null, $order);
            
            while ($object = $rs->next_result())
            {
                $catalog[$object->get_id()] = $object->get_title();
            }
        }
        
        /*
         * Add eventual empty value
         */
        if ($with_empty_value && is_array($catalog))
        {
            //$catalog = array_merge(array(DataClass :: NO_UID => ''), $catalog);
            $catalog = array('' => '') + $catalog;
        }
        
        /*
         * Translate display values
         */
        if ($translate_title)
        {
            foreach ($catalog as $value => $title)
            {
                $catalog[$value] = Translation :: get($title);
            }
        }
        
        //debug($catalog);
        

        return $catalog;
    }

    private static function get_day_catalog()
    {
        $days = array();
        
        for($i = 1; $i <= 31; $i ++)
        {
            $days[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }
        
        return $days;
    }

    private static function get_month_catalog()
    {
        $months = array();
        
        for($i = 1; $i <= 12; $i ++)
        {
            $months[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }
        
        return $months;
    }

    private static function get_year_catalog()
    {
        $years = array();
        
        for($i = date('Y') + 2; $i >= 1900; $i --)
        {
            $years[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }
        
        return $years;
    }

    private static function get_hour_catalog()
    {
        $hours = array();
        
        for($i = 0; $i < 24; $i ++)
        {
            $hours[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }
        
        return $hours;
    }

    private static function get_min_catalog()
    {
        $mins = array();
        
        for($i = 0; $i < 60; $i ++)
        {
            $mins[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }
        
        return $mins;
    }

    private static function get_sec_catalog()
    {
        $secs = array();
        
        for($i = 0; $i < 60; $i ++)
        {
            $secs[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }
        
        return $secs;
    }

}

?>