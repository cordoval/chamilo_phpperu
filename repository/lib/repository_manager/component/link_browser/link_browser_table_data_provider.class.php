<?php
/**
 * $Id: link_browser_table_data_provider.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class LinkBrowserTableDataProvider extends ObjectTableDataProvider
{	
    /**
     * The type of link
     * @var Integer
     */
	private $type;
    
    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function LinkBrowserTableDataProvider($browser, $condition, $type)
    {
        $this->type = $type;
    	parent :: __construct($browser, $condition);
    }

    /**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching content objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
		if($this->type == LinkBrowserTable :: TYPE_PUBLICATIONS)
		{
    		$order_property = $this->get_order_property($order_property);
        	$publication_attributes = $this->get_browser()->get_content_object_publication_attributes($this->get_browser()->get_user_id(), $this->get_browser()->get_object()->get_id(), null, $offset, $count, $order_property);
        	return $publication_attributes = array_splice($publication_attributes, $offset, $count);
		}
		
		if($this->type == LinkBrowserTable :: TYPE_PARENTS)
		{
			$conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_browser()->get_object()->get_id(), ComplexContentObjectItem :: get_table_name());
			
			$subselect_condition = new EqualityCondition(PortfolioItem :: PROPERTY_REFERENCE, $this->get_browser()->get_object()->get_id());
			$conditions[] = new SubSelectCondition(ComplexContentObjectItem :: PROPERTY_REF, PortfolioItem :: PROPERTY_ID, 'portfolio_item',
												   $subselect_condition);
												   
			$subselect_condition = new EqualityCondition(LearningPathItem :: PROPERTY_REFERENCE, $this->get_browser()->get_object()->get_id());
			$conditions[] = new SubSelectCondition(ComplexContentObjectItem :: PROPERTY_REF, LearningPathItem :: PROPERTY_ID, 'learning_path_item',
												   $subselect_condition);
												   
			$condition = new OrCondition($conditions);
			return RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition, array($order_property), $offset, $count);
		}
		
    	if($this->type == LinkBrowserTable :: TYPE_CHILDREN)
		{
			$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_browser()->get_object()->get_id(), ComplexContentObjectItem :: get_table_name());
			return RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items($condition, array($order_property), $offset, $count);
		}
		
    	if($this->type == LinkBrowserTable :: TYPE_ATTACHMENTS)
		{
			return RepositoryDataManager :: get_instance()->retrieve_objects_to_which_object_is_attached($this->get_browser()->get_object());
		}
		
    	if($this->type == LinkBrowserTable :: TYPE_INCLUDES)
		{
			return RepositoryDataManager :: get_instance()->retrieve_objects_in_which_object_is_included($this->get_browser()->get_object());
		}
    }

    /**
     * Gets the number of content objects in the table
     * @return int
     */
    function get_object_count()
    {
    	if($this->type == LinkBrowserTable :: TYPE_PUBLICATIONS)
		{ 
    		return $this->get_browser()->count_publication_attributes($this->get_browser()->get_user_id(), $this->get_browser()->get_object()->get_id(), $this->get_condition());
		}
		
    	if($this->type == LinkBrowserTable :: TYPE_PARENTS)
		{
			$conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $this->get_browser()->get_object()->get_id(), ComplexContentObjectItem :: get_table_name());
			
			$subselect_condition = new EqualityCondition(PortfolioItem :: PROPERTY_REFERENCE, $this->get_browser()->get_object()->get_id());
			$conditions[] = new SubSelectCondition(ComplexContentObjectItem :: PROPERTY_REF, PortfolioItem :: PROPERTY_ID, 'portfolio_item',
												   $subselect_condition);
												   
			$subselect_condition = new EqualityCondition(LearningPathItem :: PROPERTY_REFERENCE, $this->get_browser()->get_object()->get_id());
			$conditions[] = new SubSelectCondition(ComplexContentObjectItem :: PROPERTY_REF, LearningPathItem :: PROPERTY_ID, 'learning_path_item',
												   $subselect_condition);
												   
			$condition = new OrCondition($conditions);
			
			return RepositoryDataManager :: get_instance()->count_complex_content_object_items($condition);
		}
		
    	if($this->type == LinkBrowserTable :: TYPE_CHILDREN)
		{
			$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_browser()->get_object()->get_id(), ComplexContentObjectItem :: get_table_name());
			return RepositoryDataManager :: get_instance()->count_complex_content_object_items($condition);
		}
		
    	if($this->type == LinkBrowserTable :: TYPE_ATTACHMENTS)
		{
			return RepositoryDataManager :: get_instance()->count_objects_to_which_object_is_attached($this->get_browser()->get_object());
		}
		
    	if($this->type == LinkBrowserTable :: TYPE_INCLUDES)
		{
			return RepositoryDataManager :: get_instance()->count_objects_in_which_object_is_included($this->get_browser()->get_object());
		}
		
		return 0;
    }
}
?>