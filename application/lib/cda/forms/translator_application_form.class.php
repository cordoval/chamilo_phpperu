<?php
require_once dirname(__FILE__) . '/../translator_application.class.php';
require_once dirname(__FILE__) . '/../cda_language.class.php';
/**
 * $Id: language_pack_browser_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.cda.forms
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class TranslatorApplicationForm extends FormValidator
{
    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function __construct($url)
    {
        parent :: __construct('translator_application_form', 'post', $url);

        $this->build_form();
        $this->setDefaults();
    }

    /**
     * Build the form.
     */
    private function build_form()
    {  
    	$this->addElement('category', Translation :: get('LanguageSelections'));
    	
    	$platform_setting = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name('source_language', CdaManager :: APPLICATION_NAME);
    	$user_setting = UserDataManager :: get_instance()->retrieve_user_setting(Session :: get_user_id(), $platform_setting->get_id());
    	
    	if ($user_setting)
    	{
    		$language = CdaDataManager :: get_instance()->retrieve_cda_language($user_setting->get_value());
    		$this->addElement('hidden', TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID, $user_setting->get_value());
    		$this->addElement('static', null, Translation :: get('SourceLanguage'), $language->get_original_name());
    	}
    	else
    	{
    		$this->addElement('select', TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID, Translation :: get('SourceLanguage'), $this->get_source_languages());
    	}
    	
//		$url = Path :: get(WEB_PATH) . 'application/lib/cda/xml_feeds/xml_cda_languages_feed.php';
//		$locale = array();
//		$locale['Display'] = Translation :: get('SelectLanguages');
//		$locale['Searching'] = Translation :: get('Searching');
//		$locale['NoResults'] = Translation :: get('NoResults');
//		$locale['Error'] = Translation :: get('Error');
//		$hidden = false;
		
//		$elem = $this->addElement('element_finder', TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID, Translation :: get('DestinationLanguages'), $url, $locale);
//		$elem->setDefaults(array());
//		$elem->excludeElements(array());

    	$target_languages = $this->get_target_languages();
    	
    	if (count($target_languages) > 0)
    	{
    		$this->addElement('select', TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID, Translation :: get('DestinationLanguages'), $this->get_target_languages(), array('multiple'));
    	}
    	else
    	{
    		$this->addElement('static', null, Translation :: get('DestinationLanguages'), Translation :: get('NoMoreLanguagesAvailable'));
    	}

    	$this->addElement('category');
    	
    	if (count($target_languages) > 0)
    	{
        	$this->addElement('style_submit_button', 'submit', Translation :: get('Apply'), array('class' => 'positive'));
    	}
    }
    
    function get_target_languages()
    {
    	$condition = new EqualityCondition(TranslatorApplication :: PROPERTY_USER_ID, Session :: get_user_id());
    	$translator_applications = CdaDataManager :: get_instance()->retrieve_translator_applications($condition);
    	
    	$exclude = array();
    	while($translator_application = $translator_applications->next_result())
    	{
    		$exclude[] = $translator_application->get_destination_language_id();
    	}
    	
    	return $this->get_source_languages($exclude);
    }
    
    function get_source_languages($exclude = array())
    {
    	if (count($exclude) > 0)
    	{
    		$condition = new NotCondition(new InCondition(CdaLanguage :: PROPERTY_ID, $exclude));
    	}
    	else
    	{
    		$condition = null;
    	}
    	
    	$languages = CdaDataManager :: get_instance()->retrieve_cda_languages($condition, null, null, array(new ObjectTableOrder(CdaLanguage :: PROPERTY_ORIGINAL_NAME)));
    	$options = array();
    	
    	while($language = $languages->next_result())
    	{
    		$options[$language->get_id()] = $language->get_original_name();
    	}
    	
        return $options;
    }

    function setDefaults($defaults = array ())
    {
    	$user_language_text = LocalSetting :: get('platform_language');
    	$user_language = CdaDataManager :: get_instance()->retrieve_cda_languages(new EqualityCondition(CdaLanguage :: PROPERTY_ENGLISH_NAME, $user_language_text), null, 1)->next_result();
    	
    	$platform_setting = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name('source_language', CdaManager :: APPLICATION_NAME);
    	$user_setting = UserDataManager :: get_instance()->retrieve_user_setting(Session :: get_user_id(), $platform_setting->get_id());
    	
    	if ($user_setting)
    	{
    		$source_language = $user_setting->get_value();
    	}
    	else
    	{
    		if ($user_language)
    		{
    			$source_language = $user_language->get_id();
    		}
    		else
    		{
    			$source_language = LocalSetting :: get('source_language', CdaManager :: APPLICATION_NAME);
    		}
    	}
    	
    	$defaults[TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID] = $source_language;
        parent :: setDefaults($defaults);
    }
    
    function create_application()
    {
    	$values = $this->exportValues();
    	
    	$languages = $values[TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID];
    	$source_language = $values[TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID];
    	
    	foreach($languages as $language)
    	{
	   		$application = new TranslatorApplication();
	   		$application->set_user_id(Session :: get_user_id());
	   		$application->set_source_language_id($source_language);
	   		$application->set_destination_language_id($language);
	   		$application->set_date(Utilities :: to_db_date(time()));
	   		$application->set_status(TranslatorApplication :: STATUS_PENDING);
	   		
	   		if (!$application->create())
	   		{
	   			return false;
	   		}
	   		
	   		$applications[$application->get_id()] = $language;
    	}
    	
    	$this->notify($applications, $source_language);
    	
   		return true;
    }
    
    function notify($applications, $source_language)
    {
    	$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
    	
    	$html[] = sprintf(Translation :: get('UserHasAppliedForTheFollowingLanguages'), $user->get_fullname());
    	$html[] = '';
    	$html[] = Translation :: get('SourceLanguage');
    	$html[] = $this->get_language_name($source_language);
    	$html[] = '';
    	$html[] = Translation :: get('DestinationLanguages');

    	foreach($applications as $application => $language)
    	{
    		$language = $this->get_language_name($language);
    		$link = Redirect :: get_link('cda', array(Application :: PARAM_ACTION => CdaManager :: ACTION_ACTIVATE_TRANSLATOR_APPLICATION,
    											  CdaManager :: PARAM_TRANSLATOR_APPLICATION => $application));

    		$html[] = '<a href="' . $link . '">' . $language . '</a>';
    	}
    	
    	$link = Redirect :: get_link('cda', array(Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS));
    	
    	$html[] = '';
    	$html[] = Translation :: get('FollowLinkToActivate') . ':';
    	$html[] = '<a href="' . $link . '">' . $link . '</a>';
    	
    	$subject = '[CDA] ' . Translation :: get('UserAppliedForTranslator');
    	$content = implode("\n", $html);
    	$to = PlatformSetting :: get('administrator_email');
    	$mail = Mail :: factory($subject, $content, $to); 
    	$mail->send();
    }
    
    function get_language_name($language_id)
    {
    	$language = CdaDataManager :: get_instance()->retrieve_cda_language($language_id);
    	return $language->get_english_name();
    }
}
?>