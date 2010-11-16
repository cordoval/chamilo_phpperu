<?php
class ValidateScoreStepRule extends \HTML_QuickForm_Rule
{

    private $evaluation_format;

    function ValidateScoreStepRule($evaluation_format)
    {
        $this->evaluation_format = $evaluation_format;
    }

    public function validate($evaluation_score)
    {
        $quotient = intval($evaluation_score / $this->evaluation_format->get_step());
        $mod = $evaluation_score - $quotient * $this->evaluation_format->get_step();
        if ($mod != 0)
            return false;
        return true;
    }

}
?>
