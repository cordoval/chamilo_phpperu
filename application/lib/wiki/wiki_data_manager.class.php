<?php
/**
 * $Id: wiki_data_manager.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki
 */
/**
 *	This is a skeleton for a data manager for the Wiki Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Sven Vanpoucke & Stefan Billiet
 */
abstract class WikiDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function WikiDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return WikiDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'WikiDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function get_next_wiki_publication_id();

    abstract function create_wiki_publication($wiki_publication);

    abstract function update_wiki_publication($wiki_publication);

    abstract function delete_wiki_publication($wiki_publication);

    abstract function count_wiki_publications($conditions = null);

    abstract function retrieve_wiki_publication($id);

    abstract function retrieve_wiki_publications($condition = null, $offset = null, $count = null, $order_property = null);

}
?>