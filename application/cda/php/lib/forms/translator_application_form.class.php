<?php
namespace application\cda;

use common\libraries\Application;
use common\libraries\Mail;
use common\libraries\PlatformSetting;
use common\libraries\Redirect;
use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\NotCondition;
use common\libraries\InCondition;
use common\libraries\ObjectTableOrder;
use common\libraries\LocalSetting;
use common\libraries\Session;
use common\libraries\Path;
use common\libraries\Utilities;

use admin\AdminDataManager;
use user\UserDataManager;
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

        $target_languages = $this->get_target_languages();

        if (count($target_languages) > 0)
        {
            $this->addElement('select', TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID, Translation :: get('DestinationLanguages'), $this->get_target_languages(), array(
                    'multiple'));
        }
        else
        {
            $this->addElement('static', null, Translation :: get('DestinationLanguages'), Translation :: get('NoMoreLanguagesAvailable'));
        }

        $this->addElement('category');

        if (count($target_languages) > 0)
        {
            $this->addElement('style_submit_button', 'submit', Translation :: get('Apply', null, Utilities :: COMMON_LIBRARIES), array(
                    'class' => 'positive'));
        }
    }

    function get_target_languages()
    {
        $condition = new EqualityCondition(TranslatorApplication :: PROPERTY_USER_ID, Session :: get_user_id());
        $translator_applications = CdaDataManager :: get_instance()->retrieve_translator_applications($condition);

        $exclude = array();
        while ($translator_application = $translator_applications->next_result())
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

        $languages = CdaDataManager :: get_instance()->retrieve_cda_languages($condition, null, null, array(
                new ObjectTableOrder(CdaLanguage :: PROPERTY_ORIGINAL_NAME)));
        $options = array();

        while ($language = $languages->next_result())
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

        foreach ($languages as $language)
        {
            $application = new TranslatorApplication();
            $application->set_user_id(Session :: get_user_id());
            $application->set_source_language_id($source_language);
            $application->set_destination_language_id($language);
            $application->set_date(time());
            $application->set_status(TranslatorApplication :: STATUS_PENDING);

            if (! $application->create())
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

        $source_language = $this->get_language($source_language);

        foreach ($applications as $application => $language)
        {
            $language = $this->get_language($language);

            $html = array();

            $html[] = Translation :: get('DearAdministratorModerator');
            $html[] = '';
            $html[] = sprintf(Translation :: get('UserHasAppliedForTheFollowingLanguages'), $user->get_fullname());
            $html[] = '';
            $html[] = Translation :: get('SourceLanguage') . ': ' . $source_language->get_english_name();

            $language_name = $language->get_english_name();
            $link = Path :: get(WEB_PATH) . Redirect :: get_link('cda', array(
                    Application :: PARAM_ACTION => CdaManager :: ACTION_ACTIVATE_TRANSLATOR_APPLICATION,
                    CdaManager :: PARAM_TRANSLATOR_APPLICATION => $application));

            $html[] = Translation :: get('DestinationLanguage') . ': <a href="' . $link . '">' . $language_name . '</a>';

            $link = Path :: get(WEB_PATH) . Redirect :: get_link('cda', array(
                    Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS));

            $html[] = '';
            $html[] = Translation :: get('FollowLinkToActivate') . ':';
            $html[] = '<a href="' . $link . '">' . $link . '</a>';
            $html[] = '';
            $html[] = Translation :: get('KindRegards');
            $html[] = '';
            $html[] = 'Chamilo Support Team';
            $html[] = '<a href="http://www.chamilo.org">http://www.chamilo.org</a>';

            $subject = '[CDA] ' . Translation :: get('UserAppliedForTranslator');
            $content = implode("<br />", $html);

            $to = $this->get_moderator_emails($language);
            $to[] = PlatformSetting :: get('administrator_email');

            $mail = Mail :: factory($subject, $content, $to, array(
                    Mail :: NAME => 'info@chamilo.org',
                    Mail :: EMAIL => 'info@chamilo.org'));
            $mail->send();
        }

    }

    function get_language($language_id)
    {
        $language = CdaDataManager :: get_instance()->retrieve_cda_language($language_id);
        return $language;
    }

    function get_moderator_emails($cda_language)
    {
        $moderators = CdaRights :: get_allowed_users(CdaRights :: EDIT_RIGHT, $cda_language->get_id(), $cda_language->get_table_name());

        $udm = UserDataManager :: get_instance();

        $emails = array();

        foreach ($moderators as $moderator)
        {
            $user = $udm->retrieve_user($moderator);

            if (! $user)
            {
                continue;
            }

            $emails[] = $user->get_email();
        }

        return $emails;
    }
}
?>