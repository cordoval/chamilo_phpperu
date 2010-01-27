<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/variable_browser/variable_browser_table.class.php';

/**
 * cda component which allows the user to browse his variables
 * @author Sven Vanpoucke
 * @author
 */
class CdaManagerVariableTranslationViewerComponent extends CdaManagerComponent
{

	function run()
	{
		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$variable_id = Request :: get(CdaManager :: PARAM_VARIABLE);
		
		$variable = $this->retrieve_variable($variable_id);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('BrowseLanguages')));
		$trail->add(new Breadcrumb($this->get_browse_language_packs_url($language_id), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_browse_variable_translations_url($language_id, $variable->get_language_pack_id()), 
								   Translation :: get('BrowseVariableTranslations')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => $language_id,
														CdaManager :: PARAM_VARIABLE => $variable_id)), Translation :: get('ViewVariableTranslation')));

		$variable_translation = $this->retrieve_variable_translation($language_id, $variable_id);																
														
		$this->display_header($trail);

        echo '<a name="top"></a>';
        echo $this->get_action_bar_html($variable_translation) . '';
        echo '<div id="action_bar_browser">';
		echo $this->get_variable_translation_view($variable, $variable_translation);
        echo '</div>';
        
		$this->display_footer();
	}


    function get_action_bar_html($variable_translation)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
       
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Rate'), Theme :: get_common_image_path() . 'action_statistics.png', 
        			$this->get_rate_variable_translation_url($variable_translation)));
        
        if($variable_translation->get_status() == VariableTranslation :: STATUS_NORMAL)
        {
	        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Translate'), Theme :: get_common_image_path() . 'action_translate.png', 
        			$this->get_update_variable_translation_url($variable_translation)));
        			
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png', 
	        			$this->get_lock_variable_translation_url($variable_translation)));
	        $action_bar->add_common_action(new ToolbarItem(Translation :: get('UnlockNa'), Theme :: get_common_image_path() . 'action_unlock_na.png'));
        }
        else
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('TranslateNa'), Theme :: get_common_image_path() . 'action_translate_na.png'));
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('LockNa'), Theme :: get_common_image_path() . 'action_lock_na.png'));
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path() . 'action_unlock.png', 
	        			$this->get_unlock_variable_translation_url($variable_translation)));
        }
        
        return $action_bar->as_html();
    }

    function get_variable_translation_view($variable, $variable_translation)
    {
    	$html = array();
    	
    	$html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'action_publish.png);">';
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
        
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'action_statistics.png);">';
        $html[] = '<div class="title">' . Translation :: get('Statistics') . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';
        
        $html[] = '<b>' . Translation :: get('Date') . ': </b>' . Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), Utilities :: time_from_datepicker($variable_translation->get_date()));
        $html[] = '<br /><b>' . Translation :: get('Rating') . ': </b>' . $variable_translation->get_relative_rating();
        $html[] = '<br /><b>' . Translation :: get('NumberOfPersonsRated') . ': </b>' . $variable_translation->get_rated();
        $html[] = '<br /><b>' . Translation :: get('Status') . ': </b>' . $variable_translation->get_status_icon();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
    	
    	return implode("\n", $html);
    }
}
?>