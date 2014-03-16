<?php
/**
 * PHPWord
 *
 * Copyright (c) 2014 PHPWord
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 2014 PHPWord
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    0.8.0
 */

use PhpOffice\PhpWord\Exceptions\InvalidImageException;
use PhpOffice\PhpWord\Exceptions\UnsupportedImageTypeException;

/**
 * Class PHPWord_Writer_Word2007
 */
class PHPWord_Writer_Word2007 implements PHPWord_Writer_IWriter
{

    private $_document;
    private $_writerParts;
    private $_diskCachingDirectory;
    private $_useDiskCaching = false;
    private $_imageTypes = array();
    private $_objectTypes = array();

    public function __construct(PHPWord $PHPWord = null)
    {
        $this->_document = $PHPWord;

        $this->_diskCachingDirectory = './';

        $this->_writerParts['contenttypes'] = new PHPWord_Writer_Word2007_ContentTypes();
        $this->_writerParts['rels'] = new PHPWord_Writer_Word2007_Rels();
        $this->_writerParts['docprops'] = new PHPWord_Writer_Word2007_DocProps();
        $this->_writerParts['documentrels'] = new PHPWord_Writer_Word2007_DocumentRels();
        $this->_writerParts['document'] = new PHPWord_Writer_Word2007_Document();
        $this->_writerParts['styles'] = new PHPWord_Writer_Word2007_Styles();
        $this->_writerParts['header'] = new PHPWord_Writer_Word2007_Header();
        $this->_writerParts['footer'] = new PHPWord_Writer_Word2007_Footer();
        $this->_writerParts['footnotes'] = new PHPWord_Writer_Word2007_Footnotes();
        $this->_writerParts['footnotesrels'] = new PHPWord_Writer_Word2007_FootnotesRels();

        foreach ($this->_writerParts as $writer) {
            $writer->setParentWriter($this);
        }
    }

    public function save($pFilename = null)
    {
        if (!is_null($this->_document)) {

            // If $pFilename is php://output or php://stdout, make it a temporary file...
            $originalFilename = $pFilename;
            if (strtolower($pFilename) == 'php://output' || strtolower($pFilename) == 'php://stdout') {
                $pFilename = @tempnam('./', 'phppttmp');
                if ($pFilename == '') {
                    $pFilename = $originalFilename;
                }
            }

            // Create new ZIP file and open it for writing
            $zipClass = PHPWord_Settings::getZipClass();
            $objZip = new $zipClass();

            // Retrieve OVERWRITE and CREATE constants from the instantiated zip class
            // This method of accessing constant values from a dynamic class should work with all appropriate versions of PHP
            $ro = new ReflectionObject($objZip);
            $zipOverWrite = $ro->getConstant('OVERWRITE');
            $zipCreate = $ro->getConstant('CREATE');

            // Remove any existing file
            if (file_exists($pFilename)) {
                unlink($pFilename);
            }

            // Try opening the ZIP file
            if ($objZip->open($pFilename, $zipOverWrite) !== true) {
                if ($objZip->open($pFilename, $zipCreate) !== true) {
                    throw new Exception("Could not open " . $pFilename . " for writing.");
                }
            }


            $sectionElements = array();
            $_secElements = PHPWord_Media::getSectionMediaElements();
            foreach ($_secElements as $element) { // loop through section media elements
                if ($element['type'] != 'hyperlink') {
                    $this->_addFileToPackage($objZip, $element);
                }
                $sectionElements[] = $element;
            }

            $_hdrElements = PHPWord_Media::getHeaderMediaElements();
            foreach ($_hdrElements as $_headerFile => $_hdrMedia) { // loop through headers
                if (count($_hdrMedia) > 0) {
                    $objZip->addFromString('word/_rels/' . $_headerFile . '.xml.rels', $this->getWriterPart('documentrels')->writeHeaderFooterRels($_hdrMedia));
                    foreach ($_hdrMedia as $element) { // loop through header media elements
                        $this->_addFileToPackage($objZip, $element);
                    }
                }
            }

            $_ftrElements = PHPWord_Media::getFooterMediaElements();
            foreach ($_ftrElements as $_footerFile => $_ftrMedia) { // loop through footers
                if (count($_ftrMedia) > 0) {
                    $objZip->addFromString('word/_rels/' . $_footerFile . '.xml.rels', $this->getWriterPart('documentrels')->writeHeaderFooterRels($_ftrMedia));
                    foreach ($_ftrMedia as $element) { // loop through footers media elements
                        $this->_addFileToPackage($objZip, $element);
                    }
                }
            }

            $footnoteLinks = array();
            $_footnoteElements = PHPWord_Footnote::getFootnoteLinkElements();
            // loop through footnote link elements
            foreach ($_footnoteElements as $element) {
                $footnoteLinks[] = $element;
            }

            $_cHdrs = 0;
            $_cFtrs = 0;
            $rID = PHPWord_Media::countSectionMediaElements() + 6;
            $_sections = $this->_document->getSections();

            $footers = array();
            foreach ($_sections as $section) {
                $_headers = $section->getHeaders();
                foreach ($_headers as $index => &$_header) {
                    $_cHdrs++;
                    $_header->setRelationId(++$rID);
                    $_headerFile = 'header' . $_cHdrs . '.xml';
                    $sectionElements[] = array('target' => $_headerFile, 'type' => 'header', 'rID' => $rID);
                    $objZip->addFromString('word/' . $_headerFile, $this->getWriterPart('header')->writeHeader($_header));
                }

                $_footer = $section->getFooter();
                $footers[++$_cFtrs] = $_footer;
                if (!is_null($_footer)) {
                    $_footer->setRelationId(++$rID);
                    $_footerCount = $_footer->getFooterCount();
                    $_footerFile = 'footer' . $_footerCount . '.xml';
                    $sectionElements[] = array('target' => $_footerFile, 'type' => 'footer', 'rID' => $rID);
                    $objZip->addFromString('word/' . $_footerFile, $this->getWriterPart('footer')->writeFooter($_footer));
                }
            }

            if (PHPWord_Footnote::countFootnoteElements() > 0) {
                $_allFootnotesCollection = PHPWord_Footnote::getFootnoteElements();
                $_footnoteFile = 'footnotes.xml';
                $sectionElements[] = array('target'=>$_footnoteFile, 'type'=>'footnotes', 'rID'=>++$rID);
                $objZip->addFromString('word/'.$_footnoteFile, $this->getWriterPart('footnotes')->writeFootnotes($_allFootnotesCollection));
                if (count($footnoteLinks) > 0) {
                    $objZip->addFromString('word/_rels/footnotes.xml.rels', $this->getWriterPart('footnotesrels')->writeFootnotesRels($footnoteLinks));
                }
            }

            // build docx file
            // Write dynamic files
            $objZip->addFromString(
                '[Content_Types].xml',
                $this->getWriterPart('contenttypes')->writeContentTypes(
                    $this->_imageTypes,
                    $this->_objectTypes,
                    $_cHdrs,
                    $footers
                )
            );
            $objZip->addFromString('_rels/.rels', $this->getWriterPart('rels')->writeRelationships($this->_document));
            $objZip->addFromString('docProps/app.xml', $this->getWriterPart('docprops')->writeDocPropsApp($this->_document));
            $objZip->addFromString('docProps/core.xml', $this->getWriterPart('docprops')->writeDocPropsCore($this->_document));
            $objZip->addFromString('word/document.xml', $this->getWriterPart('document')->writeDocument($this->_document));
            $objZip->addFromString('word/_rels/document.xml.rels', $this->getWriterPart('documentrels')->writeDocumentRels($sectionElements));
            $objZip->addFromString('word/styles.xml', $this->getWriterPart('styles')->writeStyles($this->_document));

            // Write static files
            $objZip->addFile(PHPWORD_BASE_PATH . 'PHPWord/_staticDocParts/numbering.xml', 'word/numbering.xml');
            $objZip->addFile(PHPWORD_BASE_PATH . 'PHPWord/_staticDocParts/settings.xml', 'word/settings.xml');
            $objZip->addFile(PHPWORD_BASE_PATH . 'PHPWord/_staticDocParts/theme1.xml', 'word/theme/theme1.xml');
            $objZip->addFile(PHPWORD_BASE_PATH . 'PHPWord/_staticDocParts/webSettings.xml', 'word/webSettings.xml');
            $objZip->addFile(PHPWORD_BASE_PATH . 'PHPWord/_staticDocParts/fontTable.xml', 'word/fontTable.xml');


            // Close file
            if ($objZip->close() === false) {
                throw new Exception("Could not close zip file $pFilename.");
            }

            // If a temporary file was used, copy it to the correct file stream
            if ($originalFilename != $pFilename) {
                if (copy($pFilename, $originalFilename) === false) {
                    throw new Exception("Could not copy temporary zip file $pFilename to $originalFilename.");
                }
                @unlink($pFilename);
            }
        } else {
            throw new Exception("PHPWord object unassigned.");
        }
    }

    /**
     * @param string $src
     */
    private function checkContentTypes($src)
    {
        $extension = null;
        if (stripos(strrev($src), strrev('.php')) === 0) {
            $extension = 'php';
        } else {
            $imageType = exif_imagetype($src);
            if ($imageType === IMAGETYPE_JPEG) {
                $extension = 'jpg';
            } elseif ($imageType === IMAGETYPE_GIF) {
                $extension = 'gif';
            } elseif ($imageType === IMAGETYPE_PNG) {
                $extension = 'png';
            } elseif ($imageType === IMAGETYPE_BMP) {
                $extension = 'bmp';
            } elseif ($imageType === IMAGETYPE_TIFF_II || $imageType === IMAGETYPE_TIFF_MM) {
                $extension = 'tif';
            }
        }

        if (isset($extension)) {
            $imageData = getimagesize($src);
            $imageType = image_type_to_mime_type($imageData[2]);
            $imageExtension = str_replace('.', '', image_type_to_extension($imageData[2]));
            if ($imageExtension === 'jpeg') {
                $imageExtension = 'jpg';
            }
            if (!in_array($imageType, $this->_imageTypes)) {
                $this->_imageTypes[$imageExtension] = $imageType;
            }
        } else {
            if (!in_array($extension, $this->_objectTypes)) {
                $this->_objectTypes[] = $extension;
            }
        }
    }

    public function getWriterPart($pPartName = '')
    {
        if ($pPartName != '' && isset($this->_writerParts[strtolower($pPartName)])) {
            return $this->_writerParts[strtolower($pPartName)];
        } else {
            return null;
        }
    }

    public function getUseDiskCaching()
    {
        return $this->_useDiskCaching;
    }

    public function setUseDiskCaching($pValue = false, $pDirectory = null)
    {
        $this->_useDiskCaching = $pValue;

        if (!is_null($pDirectory)) {
            if (is_dir($pDirectory)) {
                $this->_diskCachingDirectory = $pDirectory;
            } else {
                throw new Exception("Directory does not exist: $pDirectory");
            }
        }

        return $this;
    }

    private function _addFileToPackage($objZip, $element)
    {
        if (isset($element['isMemImage']) && $element['isMemImage']) {
            $image = call_user_func($element['createfunction'], $element['source']);
            ob_start();
            call_user_func($element['imagefunction'], $image);
            $imageContents = ob_get_contents();
            ob_end_clean();
            $objZip->addFromString('word/' . $element['target'], $imageContents);
            imagedestroy($image);

            $this->checkContentTypes($element['source']);
        } else {
            $objZip->addFile($element['source'], 'word/' . $element['target']);
            $this->checkContentTypes($element['source']);
        }
    }

    /**
     * Get disk caching directory
     *
     * @return string
     */
    public function getDiskCachingDirectory()
    {
        return $this->_diskCachingDirectory;
    }
}
