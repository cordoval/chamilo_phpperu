<?php
/**
 * @package common.html.formvalidator
 */
/**
 * Objects of this class can be used to create/manipulate/validate user input.
 */
class TabbedFormValidator extends FormValidator
{
    private $tabs_generator;

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
    function TabbedFormValidator($form_name, $method = 'post', $action = '', $target = '', $attributes = null, $trackSubmit = true)
    {
        parent :: __construct($form_name, $method, $action, $target, $attributes, $trackSubmit);
        $this->tabs_generator = new DynamicFormTabsRenderer($form_name, $this);
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

    function get_tabs_generator()
    {
        return $this->tabs_generator;
    }

    /**
     * Returns the HTML representation of this form.
     */
    function toHtml()
    {
        $this->tabs_generator->render();

//        $error = false;
//        foreach ($this->_elements as $index => $element)
//        {
//            if (! is_null(parent :: getElementError($element->getName())))
//            {
//                $error = true;
//                break;
//            }
//        }
        $return_value = '';
//        if ($this->no_errors)
//        {
//            $renderer = $this->defaultRenderer();
//            $element_template = <<<EOT
//	<div class="row">
//		<div class="label">
//			<!-- BEGIN required --><span class="form_required">*</span> <!-- END required -->{label}
//		</div>
//		<div class="formw">
//			<!-- BEGIN error --><!-- END error -->	{element}
//		</div>
//	</div>
//
//EOT;
//            $renderer->setElementTemplate($element_template);
//        }
//        elseif ($error)
//        {
//            $return_value .= Display :: error_message(Translation :: get('FormHasErrorsPleaseComplete'), true);
//        }

        $return_value .= parent :: toHtml();
        // Add the div which will hold the progress bar
//        if ($this->with_progress_bar)
//        {
//            $return_value .= '<div id="dynamic_div" style="display:block; margin-left:40%; margin-top:10px;"></div>';
//        }
        return $return_value;
    }
}
?>