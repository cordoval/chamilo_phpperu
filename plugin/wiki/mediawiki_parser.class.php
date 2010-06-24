<?php
require_once dirname(__FILE__) . '/mediawiki/Sanitizer.php';
require_once dirname(__FILE__) . '/mediawiki/StringUtils.php';
require_once dirname(__FILE__) . '/mediawiki/Xml.php';
/**
 * A Mediawiki wikitext parser using the same functions
 * as used by Mediawiki's parsing engine
 *
 * @author Hans De Bisschop
 *
 */
class MediawikiParser
{
    # Constants needed for external link processing
    # Everything except bracket, space, or control characters
    const EXT_LINK_URL_CLASS = '[^][<>"\\x00-\\x20\\x7F]';
    const EXT_IMAGE_REGEX = '/^(http:\/\/|https:\/\/)([^][<>"\\x00-\\x20\\x7F]+)
		\\/([A-Za-z0-9_.,~%\\-+&;#*?!=()@\\x80-\\xFF]+)\\.((?i)gif|png|jpg|jpeg)$/Sx';

    private $wiki_page;
    private $mExtLinkBracketedRegex = '/\[(\b()[^][<>"\\x00-\\x20\\x7F]+) *([^\]\\x0a\\x0d]*?)\]/S';

    function MediawikiParser(WikiPage $wiki_page)
    {
        $this->wiki_page = $wiki_page;
    }

    function parse()
    {
        $source = $this->wiki_page->get_description();
        $source = $this->internalParse($source);

        return $source;
    }

    function internalParse($text)
    {
        $isMain = true;

        $text = $this->doHeadings($text);
        $text = $this->doAllQuotes($text);
        //        $text = $this->formatHeadings($text, $isMain);


        return $text;
    }

    /**
     * Parse headers and return html
     *
     * @private
     */
    function doHeadings($text)
    {
        for($i = 6; $i >= 1; -- $i)
        {
            $h = str_repeat('=', $i);
            $text = preg_replace("/^$h(.+)$h\\s*$/m", "<h$i>\\1</h$i>", $text);
        }
        return $text;
    }

    /**
     * Replace single quotes with HTML markup
     * @private
     * @return string the altered text
     */
    function doAllQuotes($text)
    {
        $outtext = '';
        $lines = StringUtils :: explode("\n", $text);
        foreach ($lines as $line)
        {
            $outtext .= $this->doQuotes($line) . "\n";
        }
        $outtext = substr($outtext, 0, - 1);
        return $outtext;
    }

    /**
     * Helper function for doAllQuotes()
     */
    public function doQuotes($text)
    {
        $arr = preg_split("/(''+)/", $text, - 1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($arr) == 1)
            return $text;
        else
        {
            # First, do some preliminary work. This may shift some apostrophes from
            # being mark-up to being text. It also counts the number of occurrences
            # of bold and italics mark-ups.
            $i = 0;
            $numbold = 0;
            $numitalics = 0;
            foreach ($arr as $r)
            {
                if (($i % 2) == 1)
                {
                    # If there are ever four apostrophes, assume the first is supposed to
                    # be text, and the remaining three constitute mark-up for bold text.
                    if (strlen($arr[$i]) == 4)
                    {
                        $arr[$i - 1] .= "'";
                        $arr[$i] = "'''";
                    }
                    # If there are more than 5 apostrophes in a row, assume they're all
                    # text except for the last 5.
                    else
                        if (strlen($arr[$i]) > 5)
                        {
                            $arr[$i - 1] .= str_repeat("'", strlen($arr[$i]) - 5);
                            $arr[$i] = "'''''";
                        }
                    # Count the number of occurrences of bold and italics mark-ups.
                    # We are not counting sequences of five apostrophes.
                    if (strlen($arr[$i]) == 2)
                    {
                        $numitalics ++;
                    }
                    else
                        if (strlen($arr[$i]) == 3)
                        {
                            $numbold ++;
                        }
                        else
                            if (strlen($arr[$i]) == 5)
                            {
                                $numitalics ++;
                                $numbold ++;
                            }
                }
                $i ++;
            }

            # If there is an odd number of both bold and italics, it is likely
            # that one of the bold ones was meant to be an apostrophe followed
            # by italics. Which one we cannot know for certain, but it is more
            # likely to be one that has a single-letter word before it.
            if (($numbold % 2 == 1) && ($numitalics % 2 == 1))
            {
                $i = 0;
                $firstsingleletterword = - 1;
                $firstmultiletterword = - 1;
                $firstspace = - 1;
                foreach ($arr as $r)
                {
                    if (($i % 2 == 1) and (strlen($r) == 3))
                    {
                        $x1 = substr($arr[$i - 1], - 1);
                        $x2 = substr($arr[$i - 1], - 2, 1);
                        if ($x1 === ' ')
                        {
                            if ($firstspace == - 1)
                                $firstspace = $i;
                        }
                        else
                            if ($x2 === ' ')
                            {
                                if ($firstsingleletterword == - 1)
                                    $firstsingleletterword = $i;
                            }
                            else
                            {
                                if ($firstmultiletterword == - 1)
                                    $firstmultiletterword = $i;
                            }
                    }
                    $i ++;
                }

                # If there is a single-letter word, use it!
                if ($firstsingleletterword > - 1)
                {
                    $arr[$firstsingleletterword] = "''";
                    $arr[$firstsingleletterword - 1] .= "'";
                }
                # If not, but there's a multi-letter word, use that one.
                else
                    if ($firstmultiletterword > - 1)
                    {
                        $arr[$firstmultiletterword] = "''";
                        $arr[$firstmultiletterword - 1] .= "'";
                    }
                    # ... otherwise use the first one that has neither.
                    # (notice that it is possible for all three to be -1 if, for example,
                    # there is only one pentuple-apostrophe in the line)
                    else
                        if ($firstspace > - 1)
                        {
                            $arr[$firstspace] = "''";
                            $arr[$firstspace - 1] .= "'";
                        }
            }

            # Now let's actually convert our apostrophic mush to HTML!
            $output = '';
            $buffer = '';
            $state = '';
            $i = 0;
            foreach ($arr as $r)
            {
                if (($i % 2) == 0)
                {
                    if ($state === 'both')
                        $buffer .= $r;
                    else
                        $output .= $r;
                }
                else
                {
                    if (strlen($r) == 2)
                    {
                        if ($state === 'i')
                        {
                            $output .= '</i>';
                            $state = '';
                        }
                        else
                            if ($state === 'bi')
                            {
                                $output .= '</i>';
                                $state = 'b';
                            }
                            else
                                if ($state === 'ib')
                                {
                                    $output .= '</b></i><b>';
                                    $state = 'b';
                                }
                                else
                                    if ($state === 'both')
                                    {
                                        $output .= '<b><i>' . $buffer . '</i>';
                                        $state = 'b';
                                    }
                                    else # $state can be 'b' or ''
                                    {
                                        $output .= '<i>';
                                        $state .= 'i';
                                    }
                    }
                    else
                        if (strlen($r) == 3)
                        {
                            if ($state === 'b')
                            {
                                $output .= '</b>';
                                $state = '';
                            }
                            else
                                if ($state === 'bi')
                                {
                                    $output .= '</i></b><i>';
                                    $state = 'i';
                                }
                                else
                                    if ($state === 'ib')
                                    {
                                        $output .= '</b>';
                                        $state = 'i';
                                    }
                                    else
                                        if ($state === 'both')
                                        {
                                            $output .= '<i><b>' . $buffer . '</b>';
                                            $state = 'i';
                                        }
                                        else # $state can be 'i' or ''
                                        {
                                            $output .= '<b>';
                                            $state .= 'b';
                                        }
                        }
                        else
                            if (strlen($r) == 5)
                            {
                                if ($state === 'b')
                                {
                                    $output .= '</b><i>';
                                    $state = 'i';
                                }
                                else
                                    if ($state === 'i')
                                    {
                                        $output .= '</i><b>';
                                        $state = 'b';
                                    }
                                    else
                                        if ($state === 'bi')
                                        {
                                            $output .= '</i></b>';
                                            $state = '';
                                        }
                                        else
                                            if ($state === 'ib')
                                            {
                                                $output .= '</b></i>';
                                                $state = '';
                                            }
                                            else
                                                if ($state === 'both')
                                                {
                                                    $output .= '<i><b>' . $buffer . '</b></i>';
                                                    $state = '';
                                                }
                                                else # ($state == '')
                                                {
                                                    $buffer = '';
                                                    $state = 'both';
                                                }
                            }
                }
                $i ++;
            }
            # Now close all remaining tags.  Notice that the order is important.
            if ($state === 'b' || $state === 'ib')
                $output .= '</b>';
            if ($state === 'i' || $state === 'bi' || $state === 'ib')
                $output .= '</i>';
            if ($state === 'bi')
                $output .= '</b>';
                # There might be lonely ''''', so make sure we have a buffer
            if ($state === 'both' && $buffer)
                $output .= '<b><i>' . $buffer . '</i></b>';
            return $output;
        }
    }

    /**
     * This function accomplishes several tasks:
     * 1) Auto-number headings if that option is enabled
     * 2) Add an [edit] link to sections for users who have enabled the option and can edit the page
     * 3) Add a Table of contents on the top for users who have enabled the option
     * 4) Auto-anchor headings
     *
     * It loops through all headlines, collects the necessary data, then splits up the
     * string and re-inserts the newly formatted headlines.
     *
     * @param string $text
     * @param boolean $isMain
     * @private
     */
    function formatHeadings($text, $isMain = true)
    {
        global $wgMaxTocLevel, $wgContLang, $wgEnforceHtmlIds;

        $doNumberHeadings = $this->mOptions->getNumberHeadings();
        $showEditLink = $this->mOptions->getEditSection();

        // Do not call quickUserCan unless necessary
        if ($showEditLink && ! $this->mTitle->quickUserCan('edit'))
        {
            $showEditLink = 0;
        }

        # Inhibit editsection links if requested in the page
        if (isset($this->mDoubleUnderscores['noeditsection']) || $this->mOptions->getIsPrintable())
        {
            $showEditLink = 0;
        }

        # Get all headlines for numbering them and adding funky stuff like [edit]
        # links - this is for later, but we need the number of headlines right now
        $matches = array();
        $numMatches = preg_match_all('/<H(?P<level>[1-6])(?P<attrib>.*?' . '>)(?P<header>.*?)<\/H[1-6] *>/i', $text, $matches);

        # if there are fewer than 4 headlines in the article, do not show TOC
        # unless it's been explicitly enabled.
        $enoughToc = $this->mShowToc && (($numMatches >= 4) || $this->mForceTocPosition);

        # Allow user to stipulate that a page should have a "new section"
        # link added via __NEWSECTIONLINK__
        if (isset($this->mDoubleUnderscores['newsectionlink']))
        {
            $this->mOutput->setNewSection(true);
        }

        # Allow user to remove the "new section"
        # link via __NONEWSECTIONLINK__
        if (isset($this->mDoubleUnderscores['nonewsectionlink']))
        {
            $this->mOutput->hideNewSection(true);
        }

        # if the string __FORCETOC__ (not case-sensitive) occurs in the HTML,
        # override above conditions and always show TOC above first header
        if (isset($this->mDoubleUnderscores['forcetoc']))
        {
            $this->mShowToc = true;
            $enoughToc = true;
        }

        # We need this to perform operations on the HTML
        $sk = $this->mOptions->getSkin();

        # headline counter
        $headlineCount = 0;
        $numVisible = 0;

        # Ugh .. the TOC should have neat indentation levels which can be
        # passed to the skin functions. These are determined here
        $toc = '';
        $full = '';
        $head = array();
        $sublevelCount = array();
        $levelCount = array();
        $toclevel = 0;
        $level = 0;
        $prevlevel = 0;
        $toclevel = 0;
        $prevtoclevel = 0;
        $markerRegex = "{$this->mUniqPrefix}-h-(\d+)-" . self :: MARKER_SUFFIX;
        $baseTitleText = $this->mTitle->getPrefixedDBkey();
        $tocraw = array();

        foreach ($matches[3] as $headline)
        {
            $isTemplate = false;
            $titleText = false;
            $sectionIndex = false;
            $numbering = '';
            $markerMatches = array();
            if (preg_match("/^$markerRegex/", $headline, $markerMatches))
            {
                $serial = $markerMatches[1];
                list($titleText, $sectionIndex) = $this->mHeadings[$serial];
                $isTemplate = ($titleText != $baseTitleText);
                $headline = preg_replace("/^$markerRegex/", "", $headline);
            }

            if ($toclevel)
            {
                $prevlevel = $level;
                $prevtoclevel = $toclevel;
            }
            $level = $matches[1][$headlineCount];

            if ($doNumberHeadings || $enoughToc)
            {

                if ($level > $prevlevel)
                {
                    # Increase TOC level
                    $toclevel ++;
                    $sublevelCount[$toclevel] = 0;
                    if ($toclevel < $wgMaxTocLevel)
                    {
                        $prevtoclevel = $toclevel;
                        $toc .= $sk->tocIndent();
                        $numVisible ++;
                    }
                }
                elseif ($level < $prevlevel && $toclevel > 1)
                {
                    # Decrease TOC level, find level to jump to


                    if ($toclevel == 2 && $level <= $levelCount[1])
                    {
                        # Can only go down to level 1
                        $toclevel = 1;
                    }
                    else
                    {
                        for($i = $toclevel; $i > 0; $i --)
                        {
                            if ($levelCount[$i] == $level)
                            {
                                # Found last matching level
                                $toclevel = $i;
                                break;
                            }
                            elseif ($levelCount[$i] < $level)
                            {
                                # Found first matching level below current level
                                $toclevel = $i + 1;
                                break;
                            }
                        }
                    }
                    if ($toclevel < $wgMaxTocLevel)
                    {
                        if ($prevtoclevel < $wgMaxTocLevel)
                        {
                            # Unindent only if the previous toc level was shown :p
                            $toc .= $sk->tocUnindent($prevtoclevel - $toclevel);
                            $prevtoclevel = $toclevel;
                        }
                        else
                        {
                            $toc .= $sk->tocLineEnd();
                        }
                    }
                }
                else
                {
                    # No change in level, end TOC line
                    if ($toclevel < $wgMaxTocLevel)
                    {
                        $toc .= $sk->tocLineEnd();
                    }
                }

                $levelCount[$toclevel] = $level;

                # count number of headlines for each level
                @$sublevelCount[$toclevel] ++;
                $dot = 0;
                for($i = 1; $i <= $toclevel; $i ++)
                {
                    if (! empty($sublevelCount[$i]))
                    {
                        if ($dot)
                        {
                            $numbering .= '.';
                        }
                        $numbering .= $wgContLang->formatNum($sublevelCount[$i]);
                        $dot = 1;
                    }
                }
            }

            # The safe header is a version of the header text safe to use for links
            # Avoid insertion of weird stuff like <math> by expanding the relevant sections
            $safeHeadline = $this->mStripState->unstripBoth($headline);

            # Remove link placeholders by the link text.
            #     <!--LINK number-->
            # turns into
            #     link text with suffix
            $safeHeadline = $this->replaceLinkHoldersText($safeHeadline);

            # Strip out HTML (other than plain <sup> and <sub>: bug 8393)
            $tocline = preg_replace(array('#<(?!/?(sup|sub)).*?' . '>#', '#<(/?(sup|sub)).*?' . '>#'), array('', '<$1>'), $safeHeadline);
            $tocline = trim($tocline);

            # For the anchor, strip out HTML-y stuff period
            $safeHeadline = preg_replace('/<.*?' . '>/', '', $safeHeadline);
            $safeHeadline = trim($safeHeadline);

            # Save headline for section edit hint before it's escaped
            $headlineHint = $safeHeadline;

            if ($wgEnforceHtmlIds)
            {
                $legacyHeadline = false;
                $safeHeadline = Sanitizer :: escapeId($safeHeadline, 'noninitial');
            }
            else
            {
                # For reverse compatibility, provide an id that's
                # HTML4-compatible, like we used to.
                #
                # It may be worth noting, academically, that it's possible for
                # the legacy anchor to conflict with a non-legacy headline
                # anchor on the page.  In this case likely the "correct" thing
                # would be to either drop the legacy anchors or make sure
                # they're numbered first.  However, this would require people
                # to type in section names like "abc_.D7.93.D7.90.D7.A4"
                # manually, so let's not bother worrying about it.
                $legacyHeadline = Sanitizer :: escapeId($safeHeadline, 'noninitial');
                $safeHeadline = Sanitizer :: escapeId($safeHeadline, 'xml');

                if ($legacyHeadline == $safeHeadline)
                {
                    # No reason to have both (in fact, we can't)
                    $legacyHeadline = false;
                }
                elseif ($legacyHeadline != Sanitizer :: escapeId($legacyHeadline, 'xml'))
                {
                    # The legacy id is invalid XML.  We used to allow this, but
                    # there's no reason to do so anymore.  Backward
                    # compatibility will fail slightly in this case, but it's
                    # no big deal.
                    $legacyHeadline = false;
                }
            }

            # HTML names must be case-insensitively unique (bug 10721).  FIXME:
            # Does this apply to Unicode characters?  Because we aren't
            # handling those here.
            $arrayKey = strtolower($safeHeadline);
            if ($legacyHeadline === false)
            {
                $legacyArrayKey = false;
            }
            else
            {
                $legacyArrayKey = strtolower($legacyHeadline);
            }

            # count how many in assoc. array so we can track dupes in anchors
            if (isset($refers[$arrayKey]))
            {
                $refers[$arrayKey] ++;
            }
            else
            {
                $refers[$arrayKey] = 1;
            }
            if (isset($refers[$legacyArrayKey]))
            {
                $refers[$legacyArrayKey] ++;
            }
            else
            {
                $refers[$legacyArrayKey] = 1;
            }

            # Don't number the heading if it is the only one (looks silly)
            if ($doNumberHeadings && count($matches[3]) > 1)
            {
                # the two are different if the line contains a link
                $headline = $numbering . ' ' . $headline;
            }

            # Create the anchor for linking from the TOC to the section
            $anchor = $safeHeadline;
            $legacyAnchor = $legacyHeadline;
            if ($refers[$arrayKey] > 1)
            {
                $anchor .= '_' . $refers[$arrayKey];
            }
            if ($legacyHeadline !== false && $refers[$legacyArrayKey] > 1)
            {
                $legacyAnchor .= '_' . $refers[$legacyArrayKey];
            }
            if ($enoughToc && (! isset($wgMaxTocLevel) || $toclevel < $wgMaxTocLevel))
            {
                $toc .= $sk->tocLine($anchor, $tocline, $numbering, $toclevel);
                $tocraw[] = array('toclevel' => $toclevel, 'level' => $level, 'line' => $tocline, 'number' => $numbering);
            }
            # give headline the correct <h#> tag
            if ($showEditLink && $sectionIndex !== false)
            {
                if ($isTemplate)
                {
                    # Put a T flag in the section identifier, to indicate to extractSections()
                    # that sections inside <includeonly> should be counted.
                    $editlink = $sk->doEditSectionLink(Title :: newFromText($titleText), "T-$sectionIndex");
                }
                else
                {
                    $editlink = $sk->doEditSectionLink($this->mTitle, $sectionIndex, $headlineHint);
                }
            }
            else
            {
                $editlink = '';
            }
            $head[$headlineCount] = $sk->makeHeadline($level, $matches['attrib'][$headlineCount], $anchor, $headline, $editlink, $legacyAnchor);

            $headlineCount ++;
        }

        $this->mOutput->setSections($tocraw);

        # Never ever show TOC if no headers
        if ($numVisible < 1)
        {
            $enoughToc = false;
        }

        if ($enoughToc)
        {
            if ($prevtoclevel > 0 && $prevtoclevel < $wgMaxTocLevel)
            {
                $toc .= $sk->tocUnindent($prevtoclevel - 1);
            }
            $toc = $sk->tocList($toc);
        }

        # split up and insert constructed headlines


        $blocks = preg_split('/<H[1-6].*?' . '>.*?<\/H[1-6]>/i', $text);
        $i = 0;

        foreach ($blocks as $block)
        {
            if ($showEditLink && $headlineCount > 0 && $i == 0 && $block !== "\n")
            {
                # This is the [edit] link that appears for the top block of text when
            # section editing is enabled


            # Disabled because it broke block formatting
            # For example, a bullet point in the top line
            # $full .= $sk->editSectionLink(0);
            }
            $full .= $block;
            if ($enoughToc && ! $i && $isMain && ! $this->mForceTocPosition)
            {
                # Top anchor now in skin
                $full = $full . $toc;
            }

            if (! empty($head[$i]))
            {
                $full .= $head[$i];
            }
            $i ++;
        }
        if ($this->mForceTocPosition)
        {
            return str_replace('<!--MWTOC-->', $toc, $full);
        }
        else
        {
            return $full;
        }
    }
}
?>