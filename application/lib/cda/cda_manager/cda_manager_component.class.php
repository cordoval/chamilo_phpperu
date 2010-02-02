<?php


/**
 * @package application.lib.cda.cda_manager
 * Basic functionality of a component to talk with the cda application
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
abstract class CdaManagerComponent extends WebApplicationComponent
{
	/**
	 * Constructor
	 * @param Cda $cda The cda which
	 * provides this component
	 */
	function CdaManagerComponent($cda)
	{
		parent :: __construct($cda);
	}

	//Data Retrieval

	function count_cda_languages($condition)
	{
		return $this->get_parent()->count_cda_languages($condition);
	}

	function retrieve_cda_languages($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_cda_languages($condition, $offset, $count, $order_property);
	}

 	function retrieve_cda_language($id)
	{
		return $this->get_parent()->retrieve_cda_language($id);
	}

	function count_language_packs($condition)
	{
		return $this->get_parent()->count_language_packs($condition);
	}

	function retrieve_language_packs($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_language_packs($condition, $offset, $count, $order_property);
	}

 	function retrieve_language_pack($id)
	{
		return $this->get_parent()->retrieve_language_pack($id);
	}

	function count_variables($condition)
	{
		return $this->get_parent()->count_variables($condition);
	}

	function retrieve_variables($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_variables($condition, $offset, $count, $order_property);
	}

 	function retrieve_variable($id)
	{
		return $this->get_parent()->retrieve_variable($id);
	}

	function count_variable_translations($condition)
	{
		return $this->get_parent()->count_variable_translations($condition);
	}

	function retrieve_variable_translations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_variable_translations($condition, $offset, $count, $order_property);
	}
	
 	function retrieve_variable_translation($variable_translation_id)
	{
		return $this->get_parent()->retrieve_variable_translation($variable_translation_id);
	}

 	function retrieve_variable_translation_by_parameters($language_id, $variable_id)
	{
		return $this->get_parent()->retrieve_variable_translation_by_parameters($language_id, $variable_id);
	}
	
	function can_language_be_locked($language)
	{
		return $this->get_parent()->can_language_be_locked($language);
	}
	
	function can_language_be_unlocked($language)
	{
		return $this->get_parent()->can_language_be_unlocked($language);
	}
	
	function can_language_pack_be_locked($language_pack, $language_id)
	{
		return $this->get_parent()->can_language_pack_be_locked($language_pack, $language_id);
	}
	
	function can_language_pack_be_unlocked($language_pack, $language_id)
	{
		return $this->get_parent()->can_language_pack_be_unlocked($language_pack, $language_id);
	}
	
	function get_progress_for_language($language)
	{
		return $this->get_parent()->get_progress_for_language($language);
	}
	
	function get_progress_for_language_pack($language_pack, $language_id = null)
	{
		return $this->get_parent()->get_progress_for_language_pack($language_pack, $language_id);
	}

	// Url Creation

	function get_create_cda_language_url()
	{
		return $this->get_parent()->get_create_cda_language_url();
	}

	function get_update_cda_language_url($cda_language)
	{
		return $this->get_parent()->get_update_cda_language_url($cda_language);
	}

 	function get_delete_cda_language_url($cda_language)
	{
		return $this->get_parent()->get_delete_cda_language_url($cda_language);
	}

	function get_browse_cda_languages_url()
	{
		return $this->get_parent()->get_browse_cda_languages_url();
	}

	function get_admin_browse_cda_languages_url()
	{
		return $this->get_parent()->get_admin_browse_cda_languages_url();
	}
	
	function get_create_language_pack_url()
	{
		return $this->get_parent()->get_create_language_pack_url();
	}

	function get_update_language_pack_url($language_pack)
	{
		return $this->get_parent()->get_update_language_pack_url($language_pack);
	}

 	function get_delete_language_pack_url($language_pack)
	{
		return $this->get_parent()->get_delete_language_pack_url($language_pack);
	}

	function get_browse_language_packs_url($language_id)
	{
		return $this->get_parent()->get_browse_language_packs_url($language_id);
	}
	
	function get_admin_browse_language_packs_url()
	{
		return $this->get_parent()->get_admin_browse_language_packs_url();
	}

	function get_create_variable_url($language_pack_id)
	{
		return $this->get_parent()->get_create_variable_url($language_pack_id);
	}

	function get_update_variable_url($variable)
	{
		return $this->get_parent()->get_update_variable_url($variable);
	}

 	function get_delete_variable_url($variable)
	{
		return $this->get_parent()->get_delete_variable_url($variable);
	}

	function get_browse_variables_url()
	{
		return $this->get_parent()->get_browse_variables_url();
	}
	
	function get_update_variable_translation_url($variable_translation)
	{
		return $this->get_parent()->get_update_variable_translation_url($variable_translation);
	}

	function get_browse_variable_translations_url($language_id, $language_pack_id)
	{
		return $this->get_parent()->get_browse_variable_translations_url($language_id, $language_pack_id);
	}

	function get_admin_browse_variables_url($language_pack_id)
	{
		return $this->get_parent()->get_admin_browse_variables_url($language_pack_id);
	}
	
	function retrieve_english_translation($variable_id)
	{
		return $this->get_parent()->retrieve_english_translation($variable_id);
	}
	
	function get_lock_variable_translation_url($variable_translation)
	{
		return $this->get_parent()->get_lock_variable_translation_url($variable_translation);
	}
	
	function get_lock_language_pack_url($language_pack, $language_id)
	{
		return $this->get_parent()->get_lock_language_pack_url($language_pack, $language_id);
	}
	
 	function get_lock_language_url($language)
	{
		return $this->get_parent()->get_lock_language_url($language);
	}
	
	function get_unlock_variable_translation_url($variable_translation)
	{
		return $this->get_parent()->get_unlock_variable_translation_url($variable_translation);
	}
	
	function get_unlock_language_pack_url($language_pack, $language_id)
	{
		return $this->get_parent()->get_unlock_language_pack_url($language_pack, $language_id);
	}
	
 	function get_unlock_language_url($language)
	{
		return $this->get_parent()->get_unlock_language_url($language);
	}
	
 	function get_view_variable_translation_url($variable_translation)
	{
		return $this->get_parent()->get_view_variable_translation_url($variable_translation);
	}
	
 	function get_rate_variable_translation_url($variable_translation)
	{
		return $this->get_parent()->get_rate_variable_translation_url($variable_translation);
	}
	
 	function get_export_translations_url()
	{
		return $this->get_parent()->get_export_translations_url();
	}
	
	function get_translator_application_url()
	{
		return $this->get_parent()->get_translator_application_url();
	}
	
	function get_activate_translator_application_url($translator_application)
	{
		return $this->get_parent()->get_activate_translator_application_url($translator_application);
	}
	
	function get_deactivate_translator_application_url($translator_application)
	{
		return $this->get_parent()->get_deactivate_translator_application_url($translator_application);
	}
	
	function get_delete_translator_application_url($translator_application)
	{
		return $this->get_parent()->get_delete_translator_application_url($translator_application);
	}
	
	function count_translator_applications($condition)
	{
		return $this->get_parent()->count_translator_applications($condition);
	}

	function retrieve_translator_applications($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_translator_applications($condition, $offset, $count, $order_property);
	}

 	function retrieve_translator_application($id)
	{
		return $this->get_parent()->retrieve_translator_application($id);
	}
	
 	function update_variable_translations($properties = array(), $condition, $offset = null, $max_objects = null, $order_by = array())
	{
		return $this->get_parent()->update_variable_translations($properties, $condition, $offset, $max_objects, $order_by);
	}
}
?>