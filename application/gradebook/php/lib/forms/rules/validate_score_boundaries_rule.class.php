<?php
class ValidateScoreBoundariesRule extends \HTML_QuickForm_Rule
{

    private $evaluation_format;

    function ValidateScoreBoundariesRule($evaluation_format)
    {
        $this->evaluation_format = $evaluation_format;
    }

    public function validate($evaluation_score)
    {
        if ($evaluation_score < $this->evaluation_format->get_min_value() || $evaluation_score > $this->evaluation_format->get_max_value())
            return false;
        return true;
    }

}

