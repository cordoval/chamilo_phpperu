<?php
/**
 *	This is a skeleton for a data manager for the ContextLinker Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *  @author Sven Vanpoucke
 *	@author Jens Vanderheyden
 */
Interface ContextLinkerDataManagerInterface
{
	function initialize();
	function create_storage_unit($name,$properties,$indexes);

	function get_next_context_link_id();
	function create_context_link($context_link);
	function update_context_link($context_link);
	function delete_context_link($context_link);
	function count_context_links($conditions = null);
	function retrieve_context_link($id);
	function retrieve_context_links($condition = null, $offset = null, $count = null, $order_property = null);
}
?>