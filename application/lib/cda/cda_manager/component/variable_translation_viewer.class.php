<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/historic_variable_translation_browser/historic_variable_translation_browser_table.class.php';

/**
 * cda component which allows the user to browse his variables
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerVariableTranslationViewerComponent extends CdaManager
{

	private $variable_translation;

	function run()
	{
		$variable_translation_id = Request :: get(CdaManager :: PARAM_VARIABLE_TRANSLATION);
		$this->variable_translation = $this->retrieve_variable_translation($variable_translation_id);

		$language_id = $this->variable_translation->get_language_id();
		$variable_id = $this->variable_translation->get_variable_id();
		$variable = $this->retrieve_variable($variable_id);

		$language = $this->retrieve_cda_language($language_id);
		$language_pack = $this->retrieve_language_pack($variable->get_language_pack_id());

		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb($this->get_browse_language_packs_url($language_id), $language->get_original_name()));
		$trail->add(new Breadcrumb($this->get_browse_variable_translations_url($language_id, $variable->get_language_pack_id()), $language_pack->get_branch_name() . ' - ' . $language_pack->get_name()));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $language_id,
														CdaManager :: PARAM_VARIABLE => $this->variable_translation->get_variable_id())), Translation :: get('ViewVariableTranslation')));

		$this->display_header($trail);

        echo '<a name="top"></a>';
        echo $this->get_action_bar_html($this->variable_translation) . '';
        echo '<div id="action_bar_browser">';
		echo $this->get_variable_translation_view($variable, $this->variable_translation);
        echo '</div>';

		$this->display_footer();
	}


    function get_action_bar_html($variable_translation)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Rate'), Theme :: get_common_image_path() . 'action_statistics.png',
        			$this->get_rate_variable_translation_url($this->variable_translation)));

		$can_translate = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: VIEW_RIGHT, $this->variable_translation->get_language_id(), 'cda_language');
		$can_lock = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $this->variable_translation->get_language_id(), 'cda_language');

		if (($can_translate && !$this->variable_translation->is_locked()) || $can_lock)
		{
	        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Translate'), Theme :: get_image_path() . 'action_translate.png',
        			$this->get_update_variable_translation_url($this->variable_translation)));

        	if ($this->variable_translation->is_outdated())
        	{
    	        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Verify'), Theme :: get_image_path() . 'action_verify.png',
            			$this->get_verify_variable_translation_url($this->variable_translation)));
        	}
        	else
        	{
    	        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Deprecate'), Theme :: get_image_path() . 'action_deprecate.png',
            			$this->get_deprecate_variable_translation_url($this->variable_translation)));
        	}
		}

		if($can_lock)
		{
	        if(!$this->variable_translation->is_locked())
	        {
	        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png',
		        			$this->get_lock_variable_translation_url($this->variable_translation)));
	        }
	        else
	        {
	        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path() . 'action_unlock.png',
		        			$this->get_unlock_variable_translation_url($this->variable_translation)));
	        }
		}

        return $action_bar->as_html();
    }

    function get_variable_translation_view($variable, $variable_translation)
    {
    	$html = array();

    	// General information
    	$html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_information.png);">';
        $html[] = '<div class="title">' . Translation :: get('General') . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';
        $html[] = '<b>' . Translation :: get('Variable') . ': </b>' . $variable->get_variable();

        $english = CdaDataManager :: get_instance()->retrieve_english_translation($variable->get_id());
        $english_translation = ($english && $english->get_translation() != ' ') ? $english->get_translation() : Translation :: get('NoTranslation');
        $html[] = '<br /><b>' . Translation :: get('EnglishTranslation') . ': </b>' . $english_translation;

        $translation = ($variable_translation->get_translation() != ' ') ? $variable_translation->get_translation() : Translation :: get('NoTranslation');
        $html[] = '<br /><b>' . Translation :: get('Translation') . ': </b>' . $translation;

        $user = UserDataManager :: get_instance()->retrieve_user($variable_translation->get_user_id());
        $contributor = $user ? $user->get_fullname() : Translation :: get('ContributorUnknown');
        $html[] = '<br /><b>' . Translation :: get('Translator') . ': </b>' . $contributor;

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        // Statistics
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_statistics.png);">';
        $html[] = '<div class="title">' . Translation :: get('Statistics') . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';

        $html[] = '<b>' . Translation :: get('Date') . ': </b>' . DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), Utilities :: time_from_datepicker($variable_translation->get_date()));
        $html[] = '<br /><b>' . Translation :: get('Rating') . ': </b>' . $variable_translation->get_relative_rating();
        $html[] = '<br /><b>' . Translation :: get('NumberOfPersonsRated') . ': </b>' . $variable_translation->get_rated();
        $html[] = '<br /><b>' . Translation :: get('Status') . ': </b>' . $variable_translation->get_status_icon();

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        // History
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_versions.png);">';
        $html[] = '<div class="title">' . Translation :: get('TranslationHistory') . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';

        $condition = new EqualityCondition(HistoricVariableTranslation :: PROPERTY_VARIABLE_TRANSLATION_ID, $variable_translation->get_id());
        $parameters = array_merge($this->get_parameters(), array(CdaManager :: PARAM_VARIABLE_TRANSLATION => $variable_translation->get_id()));
        $historic_table = new HistoricVariableTranslationBrowserTable($this, $parameters, $condition);
        $html[] = $historic_table->as_html();

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

//        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/cda.js');

    	return implode("\n", $html);
    }
}
?>