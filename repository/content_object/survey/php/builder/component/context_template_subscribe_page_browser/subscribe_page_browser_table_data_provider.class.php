<?php namespace repository\content_object\survey;
namespace repository\content_object\survey;

use common\libraries\ObjectTableDataProvider;

class SurveyContextTemplateSubscribePageBrowserTableDataProvider extends ObjectTableDataProvider {

	function __construct($browser, $condition) {
		parent::__construct ( $browser, $condition );
	}

	function get_objects($offset, $count, $order_property = null) {
		return RepositoryDataManager :: get_instance()->retrieve_content_objects($this->get_condition(), $offset, $max_objects, $order_by);
	}

	function get_object_count() {
		return RepositoryDataManager :: get_instance()->count_content_objects($this->get_condition());
	}
}
?>