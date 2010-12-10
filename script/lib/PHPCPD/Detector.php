<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2010, Sebastian Bergmann <sb@sebastian-bergmann.de>.
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
 * @package   phpcpd
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */

require_once 'PHPCPD/Clone.php';
require_once 'PHPCPD/CloneMap.php';

/**
 * PHPCPD code analyser.
 *
 * @author    Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: 1.3.2
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.0.0
 */
class PHPCPD_Detector
{
    /**
     * @var integer[] List of tokens to ignore
     */
    protected $tokensIgnoreList = array(
      T_INLINE_HTML => TRUE,
      T_COMMENT => TRUE,
      T_DOC_COMMENT => TRUE,
      T_OPEN_TAG => TRUE,
      T_OPEN_TAG_WITH_ECHO => TRUE,
      T_CLOSE_TAG => TRUE,
      T_WHITESPACE => TRUE
    );

    /**
     * @var ezcConsoleOutput
     */
    protected $output;

    /**
     * Constructor.
     *
     * @param ezcConsoleOutput $output
     * @since Method available since Release 1.3.0
     */
    public function __construct(ezcConsoleOutput $output = NULL)
    {
        $this->output = $output;
    }

    /**
     * Copy & Paste Detection (CPD).
     *
     * @param  Iterator|array   $files     List of files to process
     * @param  integer          $minLines  Minimum number of identical lines
     * @param  integer          $minTokens Minimum number of identical tokens
     * @return PHPCPD_CloneMap  Map of exact clones found in the list of files
     * @author Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
     */
    public function copyPasteDetection($files, $minLines = 5, $minTokens = 70)
    {
        $result   = new PHPCPD_CloneMap;
        $hashes   = array();
        $numLines = 0;

        if ($this->output !== NULL) {
            $bar = new ezcConsoleProgressbar($this->output, count($files));
            print "Processing files\n";
        }

        foreach ($files as $file) {
            $buffer    = file_get_contents($file);
            $numLines += substr_count($buffer, "\n");

            $currentTokenPositions = array();
            $currentSignature      = '';
            $tokens                = token_get_all($buffer);
            $tokenNr               = 0;
            $line                  = 1;

            unset($buffer);

            foreach (array_keys($tokens) as $key) {
                $token = $tokens[$key];

                if (is_string($token)) {
                    $line += substr_count($token, "\n");
                } else {
                    if (!isset($this->tokensIgnoreList[$token[0]])) {
                        $currentTokenPositions[$tokenNr++] = $line;

                        $currentSignature .= chr(
                          $token[0] & 255) . pack('N*', crc32($token[1])
                        );
                    }

                    $line += substr_count($token[1], "\n");
                }
            }

            $count     = count($currentTokenPositions);
            $firstLine = 0;
            $found     = FALSE;
            $tokenNr   = 0;

            if ($count > 0) {
                do {
                    $line = $currentTokenPositions[$tokenNr];

                    $hash = substr(
                      md5(
                        substr(
                          $currentSignature, $tokenNr * 5,
                          $minTokens * 5
                        ),
                        TRUE
                      ),
                      0,
                      8
                    );

                    if (isset($hashes[$hash])) {
                        $found = TRUE;

                        if ($firstLine === 0) {
                            $firstLine  = $line;
                            $firstHash  = $hash;
                            $firstToken = $tokenNr;
                        }
                    } else {
                        if ($found) {
                            $fileA      = $hashes[$firstHash][0];
                            $firstLineA = $hashes[$firstHash][1];

                            if ($line + 1 - $firstLine > $minLines &&
                                ($fileA != $file ||
                                 $firstLineA != $firstLine)) {
                                $result->addClone(
                                  new PHPCPD_Clone(
                                    $fileA,
                                    $firstLineA,
                                    $file,
                                    $firstLine,
                                    $line + 1 - $firstLine,
                                    $tokenNr + 1 - $firstToken
                                  )
                                );
                            }

                            $found     = FALSE;
                            $firstLine = 0;
                        }

                        $hashes[$hash] = array($file, $line);
                    }

                    $tokenNr++;
                } while ($tokenNr <= $count - $minTokens + 1);
            }

            if ($found) {
                $fileA      = $hashes[$firstHash][0];
                $firstLineA = $hashes[$firstHash][1];

                if ($line + 1 - $firstLine > $minLines &&
                    ($fileA != $file || $firstLineA != $firstLine)) {
                    $result->addClone(
                      new PHPCPD_Clone(
                        $fileA,
                        $firstLineA,
                        $file,
                        $firstLine,
                        $line + 1 - $firstLine,
                        $tokenNr + 1 - $firstToken
                      )
                    );
                }

                $found = FALSE;
            }

            if ($this->output !== NULL) {
                $bar->advance();
            }
        }

        if ($this->output !== NULL) {
            print "\n\n";
        }

        $result->setNumLines($numLines);

        return $result;
    }
}
?>
