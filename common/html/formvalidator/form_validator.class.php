<?php
/**
 * @package common.html.formvalidator
 */
// $Id: FormValidator.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once ('HTML/QuickForm.php');
require_once ('HTML/QuickForm/advmultiselect.php');
/**
 * Filter
 */
define('NO_HTML', 1);
define('STUDENT_HTML', 2);
define('TEACHER_HTML', 3);
define('STUDENT_HTML_FULLPAGE', 4);
define('TEACHER_HTML_FULLPAGE', 5);
/**
 * Objects of this class can be used to create/manipulate/validate user input.
 */
class FormValidator extends HTML_QuickForm
{
    private $no_errors;

    /**
     * The HTML-editors in this form
     */
    private $html_editors;

    /**
     * Constructor
     * @param string $form_name Name of the form
     * @param string $method Method ('post' (default) or 'get')
     * @param string $action Action (default is $PHP_SELF)
     * @param string $target Form's target defaults to '_self'
     * @param mixed $attributes (optional)Extra attributes for <form> tag
     * @param bool $trackSubmit (optional)Whether to track if the form was
     * submitted by adding a special hidden field (default = true)
     */
    function FormValidator($form_name, $method = 'post', $action = '', $target = '', $attributes = null, $trackSubmit = true)
    {
        if (is_null($attributes))
        {
            $attributes = array();
        }
        $attributes['onreset'] = 'resetElements()';

        $this->HTML_QuickForm($form_name, $method, $action, $target, $attributes, $trackSubmit);
        // Load some custom elements and rules
        $dir = dirname(__FILE__) . '/';
        $this->registerElementType('fckeditor_html_editor', $dir . 'Element/html_editor/fckeditor_html_editor.php', 'HTML_QuickForm_fckeditor_html_editor');
        $this->registerElementType('tinymce_html_editor', $dir . 'Element/html_editor/tinymce_html_editor.php', 'HTML_QuickForm_tinymce_html_editor');
        $this->registerElementType('html_editor', $dir . 'Element/html_editor.php', 'HTML_QuickForm_html_editor');
        $this->registerElementType('datepicker', $dir . 'Element/datepicker.php', 'HTML_QuickForm_datepicker');
        $this->registerElementType('timepicker', $dir . 'Element/timepicker.php', 'HTML_QuickForm_timepicker');
        $this->registerElementType('receivers', $dir . 'Element/receivers.php', 'HTML_QuickForm_receivers');
        $this->registerElementType('select_language', $dir . 'Element/select_language.php', 'HTML_QuickForm_Select_Language');
        $this->registerElementType('upload_or_create', $dir . 'Element/upload_or_create.php', 'HTML_QuickForm_upload_or_create');
        $this->registerElementType('element_finder', $dir . 'Element/element_finder.php', 'HTML_QuickForm_element_finder');
        $this->registerElementType('user_group_finder', $dir . 'Element/user_group_finder.php', 'HTML_QuickForm_user_group_finder');
        $this->registerElementType('option_orderer', $dir . 'Element/option_orderer.php', 'HTML_QuickForm_option_orderer');
        $this->registerElementType('category', $dir . 'Element/category.php', 'HTML_QuickForm_category');
        $this->registerElementType('style_button', $dir . 'Element/style_button.php', 'HTML_QuickForm_stylebutton');
        $this->registerElementType('style_submit_button', $dir . 'Element/style_submit_button.php', 'HTML_QuickForm_stylesubmitbutton');
        $this->registerElementType('style_reset_button', $dir . 'Element/style_reset_button.php', 'HTML_QuickForm_styleresetbutton');

        $this->registerRule('date', null, 'HTML_QuickForm_Rule_Date', $dir . 'Rule/Date.php');
        $this->registerRule('date_compare', null, 'HTML_QuickForm_Rule_DateCompare', $dir . 'Rule/DateCompare.php');
        $this->registerRule('html', null, 'HTML_QuickForm_Rule_HTML', $dir . 'Rule/HTML.php');
        $this->registerRule('username_available', null, 'HTML_QuickForm_Rule_UsernameAvailable', $dir . 'Rule/UsernameAvailable.php');
        $this->registerRule('username', null, 'HTML_QuickForm_Rule_Username', $dir . 'Rule/Username.php');
        $this->registerRule('filetype', null, 'HTML_QuickForm_Rule_Filetype', $dir . 'Rule/Filetype.php');
        $this->registerRule('disk_quota', null, 'HTML_QuickForm_Rule_DiskQuota', $dir . 'Rule/DiskQuota.php');
        $this->registerRule('max_value', null, 'HTML_QuickForm_Rule_MaxValue', $dir . 'Rule/MaxValue.php');

        $this->addElement('html', '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/reset.js"></script>');

        // Modify the default templates
        $renderer = $this->defaultRenderer();
        $form_template = <<<EOT

<form {attributes}>
{content}
	<div class="clear">
		&nbsp;
	</div>
</form>

EOT;
        $renderer->setFormTemplate($form_template);

        $element_template = array();
        $element_template[] = '<div class="row">';
        $element_template[] = '<div class="label">';
        $element_template[] = '{label}<!-- BEGIN required --><span class="form_required"><img src="' . Theme :: get_common_image_path() . 'action_required.png" alt="*" title ="*"/></span> <!-- END required -->';
        $element_template[] = '</div>';
        $element_template[] = '<div class="formw">';
        $element_template[] = '<div class="element"><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}</div>';
        $element_template[] = '<div class="form_feedback"></div></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);

        $renderer->setElementTemplate($element_template);

        $header_template = array();
        $header_template[] = '<div class="row">';
        $header_template[] = '<div class="form_header">{header}</div>';
        $header_template[] = '</div>';
        $header_template = implode("\n", $header_template);

        $renderer->setHeaderTemplate($header_template);

        HTML_QuickForm :: setRequiredNote('<span class="form_required"><img src="' . Theme :: get_common_image_path() . '/action_required.png" alt="*" title ="*"/>&nbsp;<small>' . Translation :: get('ThisFieldIsRequired') . '</small></span>');
        $required_note_template = <<<EOT
	<div class="row">
		<div class="label"></div>
		<div class="formw">{requiredNote}</div>
	</div>
EOT;
        $renderer->setRequiredNoteTemplate($required_note_template);

        foreach ($this->_submitValues as $index => & $value)
        {
            $value = Security :: remove_XSS($value);
        }
    }

    function set_error_reporting($enabled)
    {
        $this->no_errors = ! $enabled;
    }

    /**
     * Add a textfield to the form.
     * A trim-filter is attached to the field.
     * @param string $label The label for the form-element
     * @param string $name The element name
     * @param boolean $required Is the form-element required (default=true)
     * @param array $attributes Optional list of attributes for the form-element
     * @return HTML_QuickForm_input The element.
     */
    function add_textfield($name, $label, $required = true, $attributes = array())
    {
        if (! array_key_exists('size', $attributes))
        {
            $attributes['size'] = 50;
        }
        $element = $this->addElement('text', $name, $label, $attributes);
        $this->applyFilter($name, 'trim');
        if ($required)
        {
            $this->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
        }
        return $element;
    }

    function create_textfield($name, $label, $attributes = array())
    {
        if (! array_key_exists('size', $attributes))
        {
            $attributes['size'] = 50;
        }
        $element = $this->createElement('text', $name, $label, $attributes);
        return $element;
    }

    /**
     * Adds a select control to the form.
     * @param string $name The element name.
     * @param string $label The element label.
     * @param array $values Associative array of possible values.
     * @param boolean $required <code>true</code> if required (default),
     *                          <code>false</code> otherwise.
     * @param array $attributes Element attributes (optional).
     * @return HTML_QuickForm_select The element.
     */
    function add_select($name, $label, $values, $required = true, $attributes = array())
    {
        $element = $this->addElement('select', $name, $label, $values, $attributes);
        if ($required)
        {
            $this->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
        }
        return $element;
    }

    /**
     * Add a HTML-editor to the form to fill in a title.
     * A trim-filter is attached to the field.
     * A HTML-filter is attached to the field (cleans HTML)
     * A rule is attached to check for unwanted HTML
     * @param string $label The label for the form-element
     * @param string $name The element name
     * @param boolean $required Is the form-element required (default=true)
     * @return HTML_QuickForm_html_editor The element.
     */
    function add_html_editor($name, $label, $required = true, $options = array(), $attributes = array())
    {
        $html_editor = FormValidatorHtmlEditor :: factory(LocalSetting :: get('html_editor'), $name, $label, $required, $options, $attributes);
        $html_editor->set_form($this);
        $html_editor->add();
    }

    /*
     * Adds tabs to a form
     * @param array $tabs An array of tab objects that specifies the tabs that are going to be created
     * @param int $selected_tab The tab that is selected
     * @author Tristan Verheecke
     */
    function add_tabs($tabs, $selected_tab)
    {
    	$this->addElement('html', '<div id="form_tabs">');
        $this->addElement('html', '<ul>');
		foreach($tabs as $index => $tab)
		{
      		$this->addElement('html', '<li><a href="#form_tabs-'.$index.'">');
        	$this->addElement('html', '<span class="category">');
        	$this->addElement('html', '<span class="title">'.Translation :: get($tab->get_title()).'</span>');
        	$this->addElement('html', '</span>');
        	$this->addElement('html', '</a></li>');
		}
        $this->addElement('html', '</ul>');
        foreach($tabs as $index => $tab)
        {
//            $this->addElement('html', '<h2>' . $tab->get_title() . '</h2>');
        	$this->addElement('html', '<div class="form_tab" id="form_tabs-'.$index.'">');
        	call_user_func(array($this, $tab->get_method()));
        	$this->addElement('html','<div class="clear"></div>');
        	$this->addElement('html', '</div>');
        }

        $this->addElement('html', '</div>');
        $this->addElement('html', '<script type="text/javascript">');
        $this->addElement('html', '  var tabnumber = ' . $selected_tab . ';');
        $this->addElement('html', '</script>');

        $this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/form_tabs.js'));
    }

    function create_html_editor($name, $label, $options = array(), $attributes = array())
    {
        $html_editor = FormValidatorHtmlEditor :: factory(LocalSetting :: get('html_editor'), $name, $label, false, $options, $attributes);
        $html_editor->set_form($this);
        return $html_editor->create();
    }

    function register_html_editor($name)
    {
        $this->html_editors[] = $name;
    }

    function unregister_html_editor($name)
    {
        $key = array_search($name, $this->html_editors);

        if ($key)
        {
            unset($this->html_editors[$key]);
        }
    }

    function add_allowed_html_tags($full_page = false)
    {
        $html = '<br/><small><a href="#" onclick="MyWindow=window.open(' . "'" . Path :: get(WEB_LIB_PATH) . "html/allowed_html_tags.php?fullpage=" . ($full_page ? '1' : '0') . "','MyWindow','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=500,height=600,left=200,top=20'" . '); return false;">' . Translation :: get('AllowedHTMLTags') . '</a></small><br />';
        $this->addElement('html', $html);
    }

    function get_html_editors()
    {
        return $this->html_editors;
    }

    /**
     * Add a datepicker element to the form
     * A rule is added to check if the date is a valid one
     * @param string $label The label for the form-element
     * @param string $name The element name
     * @return HTML_QuickForm_datepicker The element.
     */
    function add_datepicker($name, $label, $include_time_picker = true)
    {
        $element = $this->addElement('datepicker', $name, $label, array('form_name' => $this->getAttribute('name'), 'class' => $name), $include_time_picker);
        $this->addRule($name, Translation :: get('InvalidDate'), 'date');
        return $element;
    }

    /**
     * Add a timewindow element to the form.
     * 2 datepicker elements are added and a rule to check if the first date is
     * before the second one.
     * @param string $label The label for the form-element
     * @param string $name The element name
     */
    function add_timewindow($name_1, $name_2, $label_1, $label_2, $include_time_picker = true)
    {
        $elements[] = $this->add_datepicker($name_1, $label_1, $include_time_picker);
        $elements[] = $this->add_datepicker($name_2, $label_2, $include_time_picker);
        $this->addRule(array($name_1, $name_2), Translation :: get('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte');

        return $elements;
    }

    /**
     *
     */
    function add_forever_or_timewindow($element_label = 'PublicationPeriod', $element_name_prefix = '')
    {
        $elementName = $element_name_prefix . 'forever';
        $fromName = $element_name_prefix . 'from_date';
        $toName = $element_name_prefix . 'to_date';

        $choices[] = $this->createElement('radio', $elementName, '', Translation :: get('Forever'), 1, array('id' => 'forever', 'onclick' => 'javascript:timewindow_hide(\'forever_timewindow\')'));
        $choices[] = $this->createElement('radio', $elementName, '', Translation :: get('LimitedPeriod'), 0, array('id' => 'limited', 'onclick' => 'javascript:timewindow_show(\'forever_timewindow\')'));
        $this->addGroup($choices, null, Translation :: get($element_label), '<br />', false);
        $this->addElement('html', '<div style="margin-left:25px;display:block;" id="forever_timewindow">');
        $this->add_timewindow($fromName, $toName, '', '');
        $this->addElement('html', '</div>');
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('forever');
					if (expiration.checked)
					{
						timewindow_hide('forever_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
    }

    /**
     *
     */
    function add_forever_or_expiration_date_window($element_name, $element_label = 'ExpirationDate')
    {
        $choices[] = $this->createElement('radio', 'forever', '', Translation :: get('Forever'), 1, array('onclick' => 'javascript:timewindow_hide(\'forever_timewindow\')', 'id' => 'forever'));
        $choices[] = $this->createElement('radio', 'forever', '', Translation :: get('LimitedPeriod'), 0, array('onclick' => 'javascript:timewindow_show(\'forever_timewindow\')'));
        $this->addGroup($choices, null, Translation :: get($element_label), '<br />', false);
        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="forever_timewindow">');
        $this->addElement('datepicker', $element_name, '', array('form_name' => $this->getAttribute('name')), false);
        $this->addElement('html', '</div>');
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('forever');
					if (expiration.checked)
					{
						timewindow_hide('forever_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
    }

    function add_receivers($elementName, $elementLabel, $attributes, $no_selection = 'Everybody', $legend = null)
    {
        $choices = array();
        $choices[] = $this->createElement('radio', $elementName . '_option', '', Translation :: get($no_selection), '0', array('onclick' => 'javascript:receivers_hide(\'receivers_window\')', 'id' => 'receiver'));
        $choices[] = $this->createElement('radio', $elementName . '_option', '', Translation :: get('SelectGroupsUsers'), '1', array('onclick' => 'javascript:receivers_show(\'receivers_window\')'));
        $this->addGroup($choices, null, $elementLabel, '<br />', false);
        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="receivers_window">');

        $element_finder = $this->createElement('user_group_finder', $elementName . '_elements', '', $attributes['search_url'], $attributes['locale'], $attributes['defaults']);
        $element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);

        if ($legend)
        {
            $this->addElement('static', null, null, $legend->as_html());
        }

        $this->addElement('html', '</div>');
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('receiver');
					if (expiration.checked)
					{
						receivers_hide('receivers_window');
					}
					function receivers_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function receivers_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
    }



    function add_receivers_extended($elementName, $elementLabel, $attributes, $no_selection = 'Everybody')
    {
        //made the id's variable so that multiple "receivers" items can be put on the same page
        //addes options: "system defaults" & split "everybody" into "anonymous users" and "platform users"
        //maybe an option "only me" should also be added?
        $choices = array();

        $choices[] = $this->createElement('radio', $elementName . '_option', '', Translation :: get('SystemDefaultSettings'), '0', array('onclick' => 'javascript:receivers_hide(\''. $elementName .'receivers_window\')', 'id' => $elementName . 'receiver'));
        $choices[] = $this->createElement('radio', $elementName . '_option', '', Translation :: get('AnonymousUsers'), '0', array('onclick' => 'javascript:receivers_hide(\''. $elementName .'receivers_window\')', 'id' => $elementName . 'receiver'));
        $choices[] = $this->createElement('radio', $elementName . '_option', '', Translation :: get('PortalUsers'), '0', array('onclick' => 'javascript:receivers_hide(\''. $elementName .'receivers_window\')', 'id' => $elementName . 'receiver'));

        $choices[] = $this->createElement('radio', $elementName . '_option', '', Translation :: get('SelectGroupsUsers'), '1', array('onclick' => 'javascript:receivers_show(\''. $elementName .'receivers_window\')'));
        $this->addGroup($choices, null, $elementLabel, '<br />', false);
        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="'. $elementName .'receivers_window">');

        $element_finder = $this->createElement('user_group_finder', $elementName . '_elements', '', $attributes['search_url'], $attributes['locale'], $attributes['defaults']);
        $element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);
        $this->addElement('html', '</div>');
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('receiver');
					if (expiration.checked)
					{
						receivers_hide('receivers_window');
					}
					function receivers_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function receivers_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
    }


    function add_receivers_variable($elementName, $elementLabel, $attributes, $radioArray, $defaultSelected)
    {
        //made the id's variable so that multiple "receivers" items can be put on the same page
        //addes array for radio buttons
        $choices = array();

        if(! is_array($radioArray))
        {
            $radioArray = array($radioArray);
        }

        foreach ($radioArray as $radioType)
        {
            $choices[] = $this->createElement('radio', $elementName . '_option', '', Translation :: get($radioType), $radioType, array('onclick' => 'javascript:receivers_hide(\''. $elementName .'receivers_window\')', 'id' => $elementName . 'receiver'));

        }
        $choices[] = $this->createElement('radio', $elementName . '_option', '', Translation :: get('SelectGroupsUsers'), '1', array('onclick' => 'javascript:receivers_show(\''. $elementName .'receivers_window\')', 'id' => $elementName . 'group'));
        $this->addGroup($choices, null, $elementLabel, '<br />', false);
        $idGroup = $elementName . 'group';
        $nameWindow = $elementName .'receivers_window';
        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="'. $elementName .'receivers_window">');

        $element_finder = $this->createElement('user_group_finder', $elementName . '_elements', '', $attributes['search_url'], $attributes['locale'], $attributes['defaults']);
        $element_finder->excludeElements($attributes['exclude']);

        $this->addElement($element_finder);
        $this->addElement('html', '</div>');

            $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('$idGroup');
					if (expiration.checked)
					{
						receivers_show('$nameWindow');
					}
                                        else
                                        {
                                                receivers_hide('$nameWindow')
                                        }
					function receivers_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function receivers_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");

    }

	function add_indicators($elementName, $elementLabel, $attributes)
    {
        $this->addElement('html', '<div style="display: block;" id="receivers_window">');
		$element_finder = $this->createElement('element_finder', $elementName . '_elements', '', $attributes['search_url'], $attributes['locale'], $attributes['defaults']);
		$element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);
        $this->addElement('html', '</div>');
        $this->addElement('html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					function receivers_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function receivers_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
    }


    /**
     * Add a button to the form to add resources.
     */
    function add_resource_button()
    {
        $group[] = $this->createElement('static', 'add_resource_img', null, '<img src="' . Theme :: get_common_image_path() . 'action_attachment.png" alt="' . Translation :: get('Attachment') . '"/>');
        $group[] = $this->createElement('submit', 'add_resource', Translation :: get('Attachment'), 'class="link_alike"');
        $this->addGroup($group);
    }

    /**
     * Adds a progress bar to the form.
     * Once the user submits the form, a progress bar (animated gif) is
     * displayed. The progress bar will disappear once the page has been
     * reloaded.
     * @param int $delay The number of seconds between the moment the user
     * submits the form and the start of the progress bar.
     */
    function add_progress_bar($delay = 2)
    {
        $this->with_progress_bar = true;
        $this->updateAttributes("onsubmit=\"javascript: myUpload.start('dynamic_div','" . Theme :: get_common_image_path() . "action_progress_bar.gif','" . Translation :: get('PleaseStandBy') . "','" . $this->getAttribute('id') . "');\"");
        $this->addElement('html', '<script src="' . Path :: get(WEB_LIB_PATH) . 'javascript/upload.js" type="text/javascript"></script>');
        $this->addElement('html', '<script type="text/javascript">var myUpload = new upload(' . (abs(intval($delay)) * 1000) . ');</script>');
    }

    function validate_csv($value)
    {
        include_once ('HTML/QuickForm/RuleRegistry.php');
        $registry = & HTML_QuickForm_RuleRegistry :: singleton();
        $rulenr = '-1';
        foreach ($this->_rules as $target => $rules)
        {
            $rulenr ++;
            $submitValue = $value[$rulenr];
            foreach ($rules as $elementName => $rule)
            {
                $result = $registry->validate($rule['type'], $submitValue, $rule['format'], false);
                if (! $this->isElementRequired($target))
                {
                    if (! isset($submitValue) || '' == $submitValue)
                    {
                        continue 2;
                    }
                }

                if (! $result || (! empty($rule['howmany']) && $rule['howmany'] > (int) $result))
                {

                    if (isset($rule['group']))
                    {

                        $this->_errors[$rule['group']] = $rule['message'];
                    }
                    else
                    {
                        $this->_errors[$target] = $rule['message'];
                    }
                }
            }
        }
        return (0 == count($this->_errors));
    }

    /**
     * Adds a warning message to the form.
     * @param string $label The label for the error message
     * @param string $message The actual error message
     */
    function add_warning_message($name, $label, $message, $no_margin = false)
    {
        $html = '<div id="' . $name . '" class="row"><div class="forme' . ($no_margin ? ' forme_no_margin' : '') . '">';
        if ($label)
        {
            $html .= '<b>' . $label . '</b><br />';
        }
        $html .= $message . '</div></div>';
        $this->addElement('html', $html);
    }

    /**
     * Adds an error message to the form.
     * @param string $label The label for the error message
     * @param string $message The actual error message
     */
    function add_information_message($name, $label, $message, $no_margin = false)
    {
        $html = '<div id="' . $name . '" class="row"><div class="formc' . ($no_margin ? ' formc_no_margin' : '') . '">';
        if ($label)
        {
            $html .= '<b>' . $label . '</b><br />';
        }
        $html .= $message . '</div></div>';
        $this->addElement('html', $html);
    }

	function parse_checkbox_value($value = null)
	{
		if (isset($value) && $value == 1)
		{
		    return 1;
		}
		else
		{
		    return 0;
		}
	}

    /**
     * Adds javascript code to hide a certain element.
     */
    function add_element_hider($type, $extra = null)
    {
        $html = array();
        if ($type == 'script_block')
        {
            $html[] = '<script type="text/javascript">';
            $html[] = 'function showElement(item)';
            $html[] = '{';
            $html[] = '	if (document.getElementById(item).style.display == \'block\')';
            $html[] = '  {';
            $html[] = '		document.getElementById(item).style.display = \'none\';';
            $html[] = '  }';
            $html[] = '	else';
            $html[] = '  {';
            $html[] = '		document.getElementById(item).style.display = \'block\';';
            $html[] = '		document.getElementById(item).value = \'Version comments here ...\';';
            $html[] = '	}';
            $html[] = '}';
            $html[] = '</script>';
        }
        elseif ($type == 'script_radio')
        {
            $html[] = '<script type="text/javascript">';
            $html[] = 'function showRadio(type, item)';
            $html[] = '{';
            $html[] = '	if (type == \'A\')';
            $html[] = '	{';
            $html[] = '		for (var j = item; j >= 0; j--)';
            $html[] = '		{';
            $html[] = '			var it = type + j;';
            $html[] = '			if (document.getElementById(it).style.visibility == \'hidden\')';
            $html[] = '			{';
            $html[] = '				document.getElementById(it).style.visibility = \'visible\';';
            $html[] = '			};';
            $html[] = '		}';
            $html[] = '		for (var j = item; j < ' . $extra->get_version_count() . '; j++)';
            $html[] = '		{';
            $html[] = '			var it = type + j;';
            $html[] = '			if (document.getElementById(it).style.visibility == \'visible\')';
            $html[] = '			{';
            $html[] = '				document.getElementById(it).style.visibility = \'hidden\';';
            $html[] = '			};';
            $html[] = '		}';
            $html[] = '	}';
            $html[] = '	else if (type == \'B\')';
            $html[] = '	{';
            $html[] = '		item++;';
            $html[] = '		for (var j = item; j >= 0; j--)';
            $html[] = '		{';
            $html[] = '			var it = type + j;';
            $html[] = '			if (document.getElementById(it).style.visibility == \'visible\')';
            $html[] = '			{';
            $html[] = '				document.getElementById(it).style.visibility = \'hidden\';';
            $html[] = '			};';
            $html[] = '		}';
            $html[] = '		for (var j = item; j <= ' . $extra->get_version_count() . '; j++)';
            $html[] = '		{';
            $html[] = '			var it = type + j;';
            $html[] = '			if (document.getElementById(it).style.visibility == \'hidden\')';
            $html[] = '			{';
            $html[] = '				document.getElementById(it).style.visibility = \'visible\';';
            $html[] = '			};';
            $html[] = '		}';
            $html[] = '	}';
            $html[] = '}';
            $html[] = '</script>';
        }
        elseif ($type == 'begin')
        {
            $html[] = '<div id="' . $extra . '" style="display: none;">';
        }
        elseif ($type == 'end')
        {
            $html[] = '</div>';
        }

        if (isset($html))
        {
            $this->addElement('html', implode("\n", $html));
        }
    }

    /**
     * Display the form.
     * If an element in the form didn't validate, an error message is showed
     * asking the user to complete the form.
     */
    function display()
    {
        echo $this->toHtml();
    }

    /**
     * Returns the HTML representation of this form.
     */
    function toHtml()
    {
        $error = false;
        foreach ($this->_elements as $index => $element)
        {
            if (! is_null(parent :: getElementError($element->getName())))
            {
                $error = true;
                break;
            }
        }
        $return_value = '';
        if ($this->no_errors)
        {
            $renderer = $this->defaultRenderer();
            $element_template = <<<EOT
	<div class="row">
		<div class="label">
			<!-- BEGIN required --><span class="form_required">*</span> <!-- END required -->{label}
		</div>
		<div class="formw">
			<!-- BEGIN error --><!-- END error -->	{element}
		</div>
	</div>

EOT;
            $renderer->setElementTemplate($element_template);
        }
        elseif ($error)
        {
            $return_value .= Display :: error_message(Translation :: get('FormHasErrorsPleaseComplete'), true);
        }
        $return_value .= parent :: toHtml();
        // Add the div which will hold the progress bar
        if ($this->with_progress_bar)
        {
            $return_value .= '<div id="dynamic_div" style="display:block; margin-left:40%; margin-top:10px;"></div>';
        }
        return $return_value;
    }
}

/**
 * Clean HTML
 * @param string HTML to clean
 * @param int $mode
 * @return string The cleaned HTML
 */
function html_filter($html, $mode = NO_HTML)
{
    require_once (dirname(__FILE__) . '/Rule/HTML.php');
    $allowed_tags = HTML_QuickForm_Rule_HTML :: get_allowed_tags($mode);
    $cleaned_html = kses($html, $allowed_tags);
    return $cleaned_html;
}

function html_filter_teacher($html)
{
    return html_filter($html, TEACHER_HTML);
}

function html_filter_student($html)
{
    return html_filter($html, STUDENT_HTML);
}

function html_filter_teacher_fullpage($html)
{
    return html_filter($html, TEACHER_HTML_FULLPAGE);
}

function html_filter_student_fullpage($html)
{
    return html_filter($html, STUDENT_HTML_FULLPAGE);
}

?>
