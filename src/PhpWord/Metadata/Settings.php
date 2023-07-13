<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Metadata;

use PhpOffice\PhpWord\ComplexType\ProofState;
use PhpOffice\PhpWord\ComplexType\TrackChangesView;
use PhpOffice\PhpWord\SimpleType\Zoom;
use PhpOffice\PhpWord\Style\Language;

/**
 * Setting class.
 *
 * @since 0.14.0
 * @see  http://www.datypic.com/sc/ooxml/t-w_CT_Settings.html
 */
class Settings
{
    /**
     * Magnification Setting.
     *
     * @see  http://www.datypic.com/sc/ooxml/e-w_zoom-1.html
     *
     * @var mixed either integer, in which case it treated as a percent, or one of PhpOffice\PhpWord\SimpleType\Zoom
     */
    private $zoom = 100;

    /**
     * Mirror Page Margins.
     *
     * @see http://www.datypic.com/sc/ooxml/e-w_mirrorMargins-1.html
     *
     * @var bool
     */
    private $mirrorMargins;

    /**
     * Hide spelling errors.
     *
     * @var bool
     */
    private $hideSpellingErrors = false;

    /**
     * Hide grammatical errors.
     *
     * @var bool
     */
    private $hideGrammaticalErrors = false;

    /**
     * Visibility of Annotation Types.
     *
     * @var TrackChangesView
     */
    private $revisionView;

    /**
     * Track Revisions to Document.
     *
     * @var bool
     */
    private $trackRevisions = false;

    /**
     * Do Not Use Move Syntax When Tracking Revisions.
     *
     * @var bool
     */
    private $doNotTrackMoves = false;

    /**
     * Do Not Track Formatting Revisions When Tracking Revisions.
     *
     * @var bool
     */
    private $doNotTrackFormatting = false;

    /**
     * Spelling and Grammatical Checking State.
     *
     * @var \PhpOffice\PhpWord\ComplexType\ProofState
     */
    private $proofState;

    /**
     * Document Editing Restrictions.
     *
     * @var \PhpOffice\PhpWord\Metadata\Protection
     */
    private $documentProtection;

    /**
     * Enables different header for odd and even pages.
     *
     * @var bool
     */
    private $evenAndOddHeaders = false;

    /**
     * Theme Font Languages.
     *
     * @var Language
     */
    private $themeFontLang;

    /**
     * Automatically Recalculate Fields on Open.
     *
     * @var bool
     */
    private $updateFields = false;

    /**
     * Radix Point for Field Code Evaluation.
     *
     * @var string
     */
    private $decimalSymbol = '.';

    /**
     * Automatically hyphenate document contents when displayed.
     *
     * @var null|bool
     */
    private $autoHyphenation;

    /**
     * Maximum number of consecutively hyphenated lines.
     *
     * @var null|int
     */
    private $consecutiveHyphenLimit;

    /**
     * The allowed amount of whitespace before hyphenation is applied.
     *
     * @var null|float|int
     */
    private $hyphenationZone;

    /**
     * Do not hyphenate words in all capital letters.
     *
     * @var null|bool
     */
    private $doNotHyphenateCaps;

    /**
     * 指定页眉是否被边框所包围的设置
     *
     * @var null|bool
     */
    private $bordersDoNotSurroundHeader;

    private $drawingGridHorizontalSpacing;

    /**
     * 指定页脚是否被边框所包围的设置
     *
     * @var null|bool
     */
    private $bordersDoNotSurroundFooter;

    /**
     * 用于设置默认的制表位大小，w:val 属性的值为制表位的宽度值（以磅为单位）
     *
     * @var null|integer
     */
    private $defaultTabStop;

    /**
     * @return Protection
     */
    public function getDocumentProtection()
    {
        if ($this->documentProtection == null) {
            $this->documentProtection = new Protection();
        }

        return $this->documentProtection;
    }

    /**
     * @param Protection $documentProtection
     */
    public function setDocumentProtection($documentProtection): void
    {
        $this->documentProtection = $documentProtection;
    }

    /**
     * Sets the default tab stop.
     */
    public function setDefaultTabStop($defaultTabStop)
    {
        $this->defaultTabStop = $defaultTabStop;
    }

    /**
     * @param $drawingGridHorizontalSpacing
     * @author <presleylee@qq.com>
     * @since 2023/6/27 1:54 下午
     */
    public function setDrawingGridHorizontalSpacing($drawingGridHorizontalSpacing)
    {
        $this->drawingGridHorizontalSpacing = $drawingGridHorizontalSpacing;
    }

    /**
     * @param $drawingGridVerticalSpacing
     * @author <presleylee@qq.com>
     * @since 2023/6/27 1:54 下午
     */
    public function setDrawingGridVerticalSpacing($drawingGridVerticalSpacing)
    {
        $this->drawingGridVerticalSpacing = $drawingGridVerticalSpacing;
    }

    /**
     * @param $displayHorizontalDrawingGridEvery
     * @author <presleylee@qq.com>
     * @since 2023/6/27 1:54 下午
     */
    public function setDisplayHorizontalDrawingGridEvery($displayHorizontalDrawingGridEvery)
    {
        $this->displayHorizontalDrawingGridEvery = $displayHorizontalDrawingGridEvery;
    }

    /**
     * @param $displayVerticalDrawingGridEvery
     * @author <presleylee@qq.com>
     * @since 2023/6/27 1:54 下午
     */
    public function setDisplayVerticalDrawingGridEvery($displayVerticalDrawingGridEvery)
    {
        $this->displayVerticalDrawingGridEvery = $displayVerticalDrawingGridEvery;
    }

    public function setCharacterSpacingControl($characterSpacingControl) {
        $this->characterSpacingControl = $characterSpacingControl;
    }

    public function getCharacterSpacingControl() {
        return $this->characterSpacingControl;
    }

    public function setPrintTwoOnOne($printTwoOnOne) {
        $this->printTwoOnOne = $printTwoOnOne;
    }

    public function getPrintTwoOnOne() {
        return $this->printTwoOnOne;
    }

    /**
     * @return ProofState
     */
    public function getProofState()
    {
        if ($this->proofState == null) {
            $this->proofState = new ProofState();
        }

        return $this->proofState;
    }

    /**
     * @param ProofState $proofState
     */
    public function setProofState($proofState): void
    {
        $this->proofState = $proofState;
    }

    /**
     * Are spelling errors hidden.
     *
     * @return bool
     */
    public function hasHideSpellingErrors()
    {
        return $this->hideSpellingErrors;
    }

    /**
     * Hide spelling errors.
     *
     * @param ?bool $hideSpellingErrors
     */
    public function setHideSpellingErrors($hideSpellingErrors): void
    {
        $this->hideSpellingErrors = $hideSpellingErrors === null ? true : $hideSpellingErrors;
    }

    /**
     * Are grammatical errors hidden.
     *
     * @return bool
     */
    public function hasHideGrammaticalErrors()
    {
        return $this->hideGrammaticalErrors;
    }

    /**
     * Hide grammatical errors.
     *
     * @param ?bool $hideGrammaticalErrors
     */
    public function setHideGrammaticalErrors($hideGrammaticalErrors): void
    {
        $this->hideGrammaticalErrors = $hideGrammaticalErrors === null ? true : $hideGrammaticalErrors;
    }

    /**
     * @return bool
     */
    public function hasEvenAndOddHeaders()
    {
        return $this->evenAndOddHeaders;
    }

    /**
     * @param ?bool $evenAndOddHeaders
     */
    public function setEvenAndOddHeaders($evenAndOddHeaders): void
    {
        $this->evenAndOddHeaders = $evenAndOddHeaders === null ? true : $evenAndOddHeaders;
    }

    /**
     * Get the Visibility of Annotation Types.
     *
     * @return \PhpOffice\PhpWord\ComplexType\TrackChangesView
     */
    public function getRevisionView()
    {
        return $this->revisionView;
    }

    /**
     * Set the Visibility of Annotation Types.
     *
     * @param TrackChangesView $trackChangesView
     */
    public function setRevisionView(?TrackChangesView $trackChangesView = null): void
    {
        $this->revisionView = $trackChangesView;
    }

    /**
     * @return bool
     */
    public function hasTrackRevisions()
    {
        return $this->trackRevisions;
    }

    /**
     * @param ?bool $trackRevisions
     */
    public function setTrackRevisions($trackRevisions): void
    {
        $this->trackRevisions = $trackRevisions === null ? true : $trackRevisions;
    }

    /**
     * @return bool
     */
    public function hasDoNotTrackMoves()
    {
        return $this->doNotTrackMoves;
    }

    /**
     * @param ?bool $doNotTrackMoves
     */
    public function setDoNotTrackMoves($doNotTrackMoves): void
    {
        $this->doNotTrackMoves = $doNotTrackMoves === null ? true : $doNotTrackMoves;
    }

    /**
     * @return bool
     */
    public function hasDoNotTrackFormatting()
    {
        return $this->doNotTrackFormatting;
    }

    /**
     * @param ?bool $doNotTrackFormatting
     */
    public function setDoNotTrackFormatting($doNotTrackFormatting): void
    {
        $this->doNotTrackFormatting = $doNotTrackFormatting === null ? true : $doNotTrackFormatting;
    }

    /**
     * @return mixed
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * @param mixed $zoom
     */
    public function setZoom($zoom): void
    {
        if (is_numeric($zoom)) {
            // zoom is a percentage
            $this->zoom = $zoom;
        } else {
            Zoom::validate($zoom);
            $this->zoom = $zoom;
        }
    }

    /**
     * @return bool
     */
    public function hasMirrorMargins()
    {
        return $this->mirrorMargins;
    }

    /**
     * @param bool $mirrorMargins
     */
    public function setMirrorMargins($mirrorMargins): void
    {
        $this->mirrorMargins = $mirrorMargins;
    }

    /**
     * Returns the Language.
     *
     * @return Language
     */
    public function getThemeFontLang()
    {
        return $this->themeFontLang;
    }

    /**
     * sets the Language for this document.
     *
     * @param Language $themeFontLang
     */
    public function setThemeFontLang($themeFontLang): void
    {
        $this->themeFontLang = $themeFontLang;
    }

    /**
     * @return bool
     */
    public function hasUpdateFields()
    {
        return $this->updateFields;
    }

    /**
     * @param ?bool $updateFields
     */
    public function setUpdateFields($updateFields): void
    {
        $this->updateFields = $updateFields === null ? false : $updateFields;
    }

    /**
     * Returns the Radix Point for Field Code Evaluation.
     *
     * @return string
     */
    public function getDecimalSymbol()
    {
        return $this->decimalSymbol;
    }

    /**
     * sets the Radix Point for Field Code Evaluation.
     *
     * @param string $decimalSymbol
     */
    public function setDecimalSymbol($decimalSymbol): void
    {
        $this->decimalSymbol = $decimalSymbol;
    }

    /**
     * @return null|bool
     */
    public function hasAutoHyphenation()
    {
        return $this->autoHyphenation;
    }

    /**
     * @param bool $autoHyphenation
     */
    public function setAutoHyphenation($autoHyphenation): void
    {
        $this->autoHyphenation = (bool) $autoHyphenation;
    }

    /**
     * @return null|int
     */
    public function getConsecutiveHyphenLimit()
    {
        return $this->consecutiveHyphenLimit;
    }

    /**
     * @param int $consecutiveHyphenLimit
     */
    public function setConsecutiveHyphenLimit($consecutiveHyphenLimit): void
    {
        $this->consecutiveHyphenLimit = (int) $consecutiveHyphenLimit;
    }

    /**
     * @param int $bordersDoNotSurroundHeader
     */
    public function setBordersDoNotSurroundHeader($bordersDoNotSurroundHeader): void
    {
        $this->bordersDoNotSurroundHeader = (int) $bordersDoNotSurroundHeader;
    }

    /**
     * @param int $bordersDoNotSurroundHeader
     */
    public function setBordersDoNotSurroundFooter($bordersDoNotSurroundFooter): void
    {
        $this->bordersDoNotSurroundFooter = (int) $bordersDoNotSurroundFooter;
    }

    /**
     * @param int $bordersDoNotSurroundHeader
     */
    public function getBordersDoNotSurroundHeader()
    {
        return $this->bordersDoNotSurroundHeader;
    }

    /**
     * @param int $bordersDoNotSurroundHeader
     */
    public function getBordersDoNotSurroundFooter()
    {
        return $this->bordersDoNotSurroundHeader;
    }

    public function getDefaultTabStop()
    {
        return $this->defaultTabStop;
    }


    /**
     * @return null|float|int
     */
    public function getHyphenationZone()
    {
        return $this->hyphenationZone;
    }

    /**
     * @param null|float|int $hyphenationZone Measurement unit is twip
     */
    public function setHyphenationZone($hyphenationZone): void
    {
        $this->hyphenationZone = $hyphenationZone;
    }

    /**
     * @return null|bool
     */
    public function hasDoNotHyphenateCaps()
    {
        return $this->doNotHyphenateCaps;
    }

    /**
     * @param bool $doNotHyphenateCaps
     */
    public function setDoNotHyphenateCaps($doNotHyphenateCaps): void
    {
        $this->doNotHyphenateCaps = (bool) $doNotHyphenateCaps;
    }

    /**
     * @param $drawingGridHorizontalSpacing
     * @author <presleylee@qq.com>
     * @since 2023/6/27 1:54 下午
     */
    public function getDrawingGridHorizontalSpacing()
    {
        return $this->drawingGridHorizontalSpacing;
    }

    /**
     * @param $drawingGridVerticalSpacing
     * @author <presleylee@qq.com>
     * @since 2023/6/27 1:54 下午
     */
    public function getDrawingGridVerticalSpacing()
    {
        return $this->drawingGridVerticalSpacing;
    }

    /**
     * @param $drawingGridHorizontalSpacing
     * @author <presleylee@qq.com>
     * @since 2023/6/27 1:54 下午
     */
    public function getDisplayHorizontalDrawingGridEvery()
    {
        return $this->displayHorizontalDrawingGridEvery;
    }

    /**
     * @param $drawingGridVerticalSpacing
     * @author <presleylee@qq.com>
     * @since 2023/6/27 1:54 下午
     */
    public function getDisplayVerticalDrawingGridEvery()
    {
        return $this->displayVerticalDrawingGridEvery;
    }

    /**
     * Hide spelling errors.
     *
     * @param ?bool $hideSpellingErrors
     */
    public function setDoNotValidateAgainstSchema($doNotValidateAgainstSchema): void
    {
        $this->doNotValidateAgainstSchema = $doNotValidateAgainstSchema === null ? true : $doNotValidateAgainstSchema;
    }

    public function getDoNotValidateAgainstSchema()
    {
        return $this->doNotValidateAgainstSchema;
    }

    /**
     * Hide spelling errors.
     *
     * @param ?bool $hideSpellingErrors
     */
    public function setDoNotDemarcateInvalidXml($doNotDemarcateInvalidXml)
    {
        $this->doNotDemarcateInvalidXml = $doNotDemarcateInvalidXml === null ? true : $doNotDemarcateInvalidXml;
    }

    public function getDoNotDemarcateInvalidXml()
    {
        return $this->doNotDemarcateInvalidXml;
    }

}
