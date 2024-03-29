<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2011, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit
 * @subpackage Framework_Constraint
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.5.0
 */

/**
 * ...
 *
 * @package    PHPUnit
 * @subpackage Framework_Constraint
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 3.5.9
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.5.0
 */
class PHPUnit_Framework_Constraint_StringMatches extends PHPUnit_Framework_Constraint_PCREMatch
{
    /**
     * @var string
     */
    protected $string;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->pattern = preg_quote(preg_replace('/\r\n/', "\n", $string), '/');
        $this->pattern = str_replace(
          array(
            '%e',
            '%s',
            '%S',
            '%a',
            '%A',
            '%w',
            '%i',
            '%d',
            '%x',
            '%f',
            '%c'
          ),
          array(
            '\\' . DIRECTORY_SEPARATOR,
            '[^\r\n]+',
            '[^\r\n]*',
            '.+',
            '.*',
            '\s*',
            '[+-]?\d+',
            '\d+',
            '[0-9a-fA-F]+',
            '[+-]?\.?\d+\.?\d*(?:[Ee][+-]?\d+)?',
            '.'
          ),
          $this->pattern
        );

        $this->pattern = '/^' . $this->pattern . '$/s';
        $this->string  = $string;
    }

    /**
     * Creates the appropriate exception for the constraint which can be caught
     * by the unit test system. This can be called if a call to evaluate()
     * fails.
     *
     * @param   mixed   $other The value passed to evaluate() which failed the
     *                         constraint check.
     * @param   string  $description A string with extra description of what was
     *                               going on while the evaluation failed.
     * @param   boolean $not Flag to indicate negation.
     * @throws  PHPUnit_Framework_ExpectationFailedException
     */
    public function fail($other, $description, $not = FALSE)
    {
        $failureDescription = $this->failureDescription(
          $other,
          $description,
          $not
        );

        if (!$not) {
            throw new PHPUnit_Framework_ExpectationFailedException(
              $failureDescription,
              PHPUnit_Framework_ComparisonFailure::diffEqual(
                $this->string, $other
              ),
              $description
            );
        } else {
            throw new PHPUnit_Framework_ExpectationFailedException(
              $failureDescription,
              NULL
            );
        }
    }
}
