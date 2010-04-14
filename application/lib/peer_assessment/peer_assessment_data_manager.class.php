<?php
/**
 *	This is a skeleton for a data manager for the PeerAssessment Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Nick Van Loocke
 */
abstract class PeerAssessmentDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function PeerAssessmentDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return PeerAssessmentDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'PeerAssessmentDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
    
    
    // Abstracte methodes

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function create_peer_assessment_publication($peer_assessment_publication);

    abstract function update_peer_assessment_publication($peer_assessment_publication);

    abstract function delete_peer_assessment_publication($peer_assessment_publication);

    abstract function count_peer_assessment_publications($conditions = null);

    abstract function retrieve_peer_assessment_publication($id);

    abstract function retrieve_peer_assessment_publications($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_peer_assessment_publication_category($peer_assessment_publication);

    abstract function update_peer_assessment_publication_category($peer_assessment_publication);

    abstract function delete_peer_assessment_publication_category($peer_assessment_publication);

    abstract function count_peer_assessment_publication_categories($conditions = null);

    abstract function retrieve_peer_assessment_publication_categories($condition = null, $offset = null, $count = null, $order_property = null);
    
}
?>