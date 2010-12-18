<?php
use common\libraries\LocalSetting;
use common\libraries\Authentication;
use common\libraries\FormValidatorHtmlEditorTemplates;

require_once dirname(__FILE__) . '/../../global.inc.php';

$html_editor_templates = FormValidatorHtmlEditorTemplates :: factory(LocalSetting :: get('html_editor'));

if (Authentication :: is_valid() && $html_editor_templates->template_object_exists())
{
    echo $html_editor_templates->render();
}
else
{
    echo $html_editor_templates->render_default_templates();
}
?>