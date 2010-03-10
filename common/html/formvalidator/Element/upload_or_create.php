<?php
/**
 * @package common.html.formvalidator.Element
 */
// $Id: upload_or_create.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once 'HTML/QuickForm/group.php';
require_once 'HTML/QuickForm/radio.php';
require_once 'HTML/QuickForm/file.php';
require_once 'HTML/QuickForm/checkbox.php';
require_once dirname(__FILE__) . '/html_editor/fckeditor_html_editor.php';
/**
 * Form element to upload or create a document
 * This element contains 2 radio-
 * buttons. One with label 'upload document' and one with label 'create
 * document'. Only if the second radio-button is selected, a HTML-editor appears
 * to allow the user to create a HTML document
 */
class HTML_QuickForm_upload_or_create extends HTML_QuickForm_group
{

    /**
     * Constructor
     * @param string $elementName
     * @param string $elementLabel
     * @param array $attributes This should contain the keys 'receivers' and
     * 'receivers_selected'
     */
    function HTML_QuickForm_upload_or_create($elementName = null, $elementLabel = null, $attributes = null)
    {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = false;
        $this->_type = 'upload_or_create';
    }

    /**
     * Create the form elements to build this element group
     */
    function _createElements()
    {
        $this->_elements[0] = new HTML_QuickForm_Radio('choice', '', Translation :: get('Upload'), '0', array('onclick' => 'javascript:editor_hide(\'editor_html_content\'); javascript:uncompress_show(\'uncompress\')'));
        $this->_elements[0]->setChecked(true);
        $this->_elements[1] = new HTML_QuickForm_file('file', '');
        $this->_elements[2] = new HTML_QuickForm_Radio('choice', '', Translation :: get('Create'), '1', array('onclick' => 'javascript:editor_show(\'editor_html_content\'); javascript:editor_hide(\'uncompress\')'));
        //$this->_elements[3] = new HTML_QuickForm_html_editor('html_content', '', null, array('height' => '500'));
        //$this->_elements[3]->fullPage = true;
        $this->_elements[3] = new HTML_QuickForm_textarea('html_content', '');
        $this->_elements[4] = new HTML_QuickForm_checkbox('uncompress', '', Translation :: get('Uncompress'), array('id' => 'uncompress'));
    }

    /**
     * HTML representation
     */
    function toHtml()
    {
        $html[] = $this->_elements[0]->toHtml();
        $html[] = '<div style="display: inline;" id="uncompress">';
        $html[] = $this->_elements[1]->toHtml();
        $html[] = $this->_elements[4]->toHtml();
        $html[] = '</div>';
        $html[] = '<br />';
        $html[] = $this->_elements[2]->toHtml();
        $html[] = '<div style="margin-left:20px;display:block;" id="editor_html_content">';
        //$html[] = $this->_elements[3]->toHtml();
        $html_editor = FormValidatorHtmlEditor :: factory(LocalSetting :: get('html_editor'), 'html_content', '', false, array(FormValidatorHtmlEditorOptions :: OPTION_HEIGHT => '500', 
        			FormValidatorHtmlEditorOptions :: OPTION_FULL_PAGE => true));
        $html[] = $html_editor->render();
        $html[] = '</div>';
        $html[] = $this->getElementJS();
        return implode("\n", $html);
    }

    /**
     * Get the necessary javascript
     */
    function getElementJS()
    {
        $js = "<script language=\"JavaScript\" type=\"text/javascript\">
					editor_hide('editor_html_content');
					function editor_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function uncompress_show(item) {
						el = document.getElementById(item);
						el.style.display='inline';
					}
					function editor_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					</script>
				";
        return $js;
    }

    /**
     * accept renderer
     */
    function accept($renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }
}
?>