<?php 

class SurveyContextTemplateRelPage extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * SurveyContextTemplateRelPage properties
	 */
	
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_PAGE_ID = 'page_id';
	const PROPERTY_TEMPLATE_ID = 'template_id';

	
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_SURVEY_ID ,self :: PROPERTY_PAGE_ID, self :: PROPERTY_TEMPLATE_ID);
	}

	function get_data_manager()
	{
		return SurveyContextDataManager :: get_instance();
	}
	
/**
	 * Returns the survey_id of this SurveyContextTemplateRelPage.
	 * @return the survey_id.
	 */
	function get_survey_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
	}

	/**
	 * Sets the survey_id of this SurveyContextTemplateRelPage.
	 * @param survey_id
	 */
	function set_survey_id($survey_id)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_ID, $survey_id);
	}
	
	/**
	 * Returns the page_id of this SurveyContextTemplateRelPage.
	 * @return the page_id.
	 */
	function get_page_id()
	{
		return $this->get_default_property(self :: PROPERTY_PAGE_ID);
	}

	/**
	 * Sets the page_id of this SurveyContextTemplateRelPage.
	 * @param page_id
	 */
	function set_page_id($page_id)
	{
		$this->set_default_property(self :: PROPERTY_PAGE_ID, $page_id);
	}

	/**
	 * Returns the template_id of this SurveyContextTemplateRelPage.
	 * @return the template_id.
	 */
	function get_template_id()
	{
		return $this->get_default_property(self :: PROPERTY_TEMPLATE_ID);
	}

	/**
	 * Sets the template_id of this SurveyContextTemplateRelPage.
	 * @param template_id
	 */
	function set_template_id($template_id)
	{
		$this->set_default_property(self :: PROPERTY_TEMPLATE_ID, $template_id);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>