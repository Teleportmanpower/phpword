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
 * @copyright   2010-2018 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord;

/**
 * @covers \PhpOffice\PhpWord\TemplateProcessor
 * @coversDefaultClass \PhpOffice\PhpWord\TemplateProcessor
 * @runTestsInSeparateProcesses
 */
final class TemplateProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Template can be saved in temporary location.
     *
     * @covers ::save
     * @test
     */
    final public function testTemplateCanBeSavedInTemporaryLocation()
    {
        $templateFqfn = __DIR__ . '/_files/templates/with_table_macros.docx';

        $templateProcessor = new TemplateProcessor($templateFqfn);
        $xslDomDocument = new \DOMDocument();
        $xslDomDocument->load(__DIR__ . '/_files/xsl/remove_tables_by_needle.xsl');
        foreach (array('${employee.', '${scoreboard.', '${reference.') as $needle) {
            $templateProcessor->applyXslStyleSheet($xslDomDocument, array('needle' => $needle));
        }

        $documentFqfn = $templateProcessor->save();

        $this->assertNotEmpty($documentFqfn, 'FQFN of the saved document is empty.');
        $this->assertFileExists($documentFqfn, "The saved document \"{$documentFqfn}\" doesn't exist.");

        $templateZip = new \ZipArchive();
        $templateZip->open($templateFqfn);
        $templateHeaderXml = $templateZip->getFromName('word/header1.xml');
        $templateMainPartXml = $templateZip->getFromName('word/document.xml');
        $templateFooterXml = $templateZip->getFromName('word/footer1.xml');
        if (false === $templateZip->close()) {
            throw new \Exception("Could not close zip file \"{$templateZip}\".");
        }

        $documentZip = new \ZipArchive();
        $documentZip->open($documentFqfn);
        $documentHeaderXml = $documentZip->getFromName('word/header1.xml');
        $documentMainPartXml = $documentZip->getFromName('word/document.xml');
        $documentFooterXml = $documentZip->getFromName('word/footer1.xml');
        if (false === $documentZip->close()) {
            throw new \Exception("Could not close zip file \"{$documentZip}\".");
        }

        $this->assertNotEquals($templateHeaderXml, $documentHeaderXml);
        $this->assertNotEquals($templateMainPartXml, $documentMainPartXml);
        $this->assertNotEquals($templateFooterXml, $documentFooterXml);

        return $documentFqfn;
    }

    /**
     * XSL stylesheet can be applied.
     *
     * @test
     * @covers ::applyXslStyleSheet
     * @depends testTemplateCanBeSavedInTemporaryLocation
     *
     * @param string $actualDocumentFqfn
     *
     * @throws \Exception
     */
    final public function testXslStyleSheetCanBeApplied($actualDocumentFqfn)
    {
        $expectedDocumentFqfn = __DIR__ . '/_files/documents/without_table_macros.docx';

        $actualDocumentZip = new \ZipArchive();
        $actualDocumentZip->open($actualDocumentFqfn);
        $actualHeaderXml = $actualDocumentZip->getFromName('word/header1.xml');
        $actualMainPartXml = $actualDocumentZip->getFromName('word/document.xml');
        $actualFooterXml = $actualDocumentZip->getFromName('word/footer1.xml');
        if (false === $actualDocumentZip->close()) {
            throw new \Exception("Could not close zip file \"{$actualDocumentFqfn}\".");
        }

        $expectedDocumentZip = new \ZipArchive();
        $expectedDocumentZip->open($expectedDocumentFqfn);
        $expectedHeaderXml = $expectedDocumentZip->getFromName('word/header1.xml');
        $expectedMainPartXml = $expectedDocumentZip->getFromName('word/document.xml');
        $expectedFooterXml = $expectedDocumentZip->getFromName('word/footer1.xml');
        if (false === $expectedDocumentZip->close()) {
            throw new \Exception("Could not close zip file \"{$expectedDocumentFqfn}\".");
        }

        $this->assertXmlStringEqualsXmlString($expectedHeaderXml, $actualHeaderXml);
        $this->assertXmlStringEqualsXmlString($expectedMainPartXml, $actualMainPartXml);
        $this->assertXmlStringEqualsXmlString($expectedFooterXml, $actualFooterXml);
    }

    /**
     * XSL stylesheet cannot be applied on failure in setting parameter value.
     *
     * @covers                   ::applyXslStyleSheet
     * @expectedException        \PhpOffice\PhpWord\Exception\Exception
     * @expectedExceptionMessage Could not set values for the given XSL style sheet parameters.
     * @test
     */
    final public function testXslStyleSheetCanNotBeAppliedOnFailureOfSettingParameterValue()
    {
        $templateProcessor = new TemplateProcessor(__DIR__ . '/_files/templates/blank.docx');

        $xslDomDocument = new \DOMDocument();
        $xslDomDocument->load(__DIR__ . '/_files/xsl/passthrough.xsl');

        /*
         * We have to use error control below, because \XSLTProcessor::setParameter omits warning on failure.
         * This warning fails the test.
         */
        @$templateProcessor->applyXslStyleSheet($xslDomDocument, array(1 => 'somevalue'));
    }

    /**
     * XSL stylesheet can be applied on failure of loading XML from template.
     *
     * @covers                   ::applyXslStyleSheet
     * @expectedException        \PhpOffice\PhpWord\Exception\Exception
     * @expectedExceptionMessage Could not load the given XML document.
     * @test
     */
    final public function testXslStyleSheetCanNotBeAppliedOnFailureOfLoadingXmlFromTemplate()
    {
        $templateProcessor = new TemplateProcessor(__DIR__ . '/_files/templates/corrupted_main_document_part.docx');

        $xslDomDocument = new \DOMDocument();
        $xslDomDocument->load(__DIR__ . '/_files/xsl/passthrough.xsl');

        /*
         * We have to use error control below, because \DOMDocument::loadXML omits warning on failure.
         * This warning fails the test.
         */
        @$templateProcessor->applyXslStyleSheet($xslDomDocument);
    }

    /**
     * @covers ::setValue
     * @covers ::cloneRow
     * @covers ::saveAs
     * @test
     */
    public function testCloneRow()
    {
        $templateProcessor = new TemplateProcessor(__DIR__ . '/_files/templates/clone-merge.docx');

        $this->assertEquals(
            array('tableHeader', 'userId', 'userName', 'userLocation'),
            $templateProcessor->getVariables()
        );

        $docName = 'clone-test-result.docx';
        $templateProcessor->setValue('tableHeader', utf8_decode('ééé'));
        $templateProcessor->cloneRow('userId', 1);
        $templateProcessor->setValue('userId#1', 'Test');
        $templateProcessor->saveAs($docName);
        $docFound = file_exists($docName);
        unlink($docName);
        $this->assertTrue($docFound);
    }

    /**
     * @covers ::setValue
     * @covers ::saveAs
     * @test
     */
    public function testMacrosCanBeReplacedInHeaderAndFooter()
    {
        $templateProcessor = new TemplateProcessor(__DIR__ . '/_files/templates/header-footer.docx');

        $this->assertEquals(array('documentContent', 'headerValue', 'footerValue'), $templateProcessor->getVariables());

        $macroNames = array('headerValue', 'documentContent', 'footerValue');
        $macroValues = array('Header Value', 'Document text.', 'Footer Value');
        $templateProcessor->setValue($macroNames, $macroValues);

        $docName = 'header-footer-test-result.docx';
        $templateProcessor->saveAs($docName);
        $docFound = file_exists($docName);
        unlink($docName);
        $this->assertTrue($docFound);
    }

    /**
     * @covers ::cloneBlock
     * @covers ::deleteBlock
     * @covers ::saveAs
     * @test
     */
    public function testCloneDeleteBlock()
    {
        $templateProcessor = new TemplateProcessor(__DIR__ . '/_files/templates/clone-delete-block.docx');

        $docName = 'clone-delete-block-result.docx';
        $templateProcessor->cloneBlock('CLONEME', 3);
        $templateProcessor->cloneBlock('CLONEMEONCE');
        $templateProcessor->deleteBlock('DELETEME');
        $templateProcessor->replaceBlock('REPLACEME', '
          <w:p>
            <w:pPr>
              <w:pStyle w:val="Normal"/>
              <w:rPr/>
            </w:pPr>
            <w:r>
              <w:rPr/>
              <w:t>You have been replaced!</w:t>
            </w:r>
          </w:p>');
        $templateProcessor->saveAs($docName);
        $docFound = file_exists($docName);
        unlink($docName);
        $this->assertTrue($docFound);
    }

    /**
     * @covers ::getVariableCount
     * @test
     */
    public function getVariableCountCountsHowManyTimesEachPlaceholderIsPresent()
    {
        // create template with placeholders
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $header = $section->addHeader();
        $header->addText('${a_field_that_is_present_three_times}');
        $footer = $section->addFooter();
        $footer->addText('${a_field_that_is_present_twice}');
        $section2 = $phpWord->addSection();
        $section2->addText('
                ${a_field_that_is_present_one_time}
                  ${a_field_that_is_present_three_times}
              ${a_field_that_is_present_twice}
                   ${a_field_that_is_present_three_times}
        ');
        $objWriter = IOFactory::createWriter($phpWord);
        $templatePath = 'test.docx';
        $objWriter->save($templatePath);

        $templateProcessor = new TemplateProcessor($templatePath);
        $variableCount = $templateProcessor->getVariableCount();
        unlink($templatePath);

        $this->assertEquals(
            array(
                'a_field_that_is_present_three_times' => 3,
                'a_field_that_is_present_twice'       => 2,
                'a_field_that_is_present_one_time'    => 1,
            ),
            $variableCount
        );
    }

    /**
     * @covers ::cloneBlock
     * @test
     */
    public function cloneBlockCanCloneABlockTwice()
    {
        // create template with placeholders and block
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $documentElements = array(
            'Title: ${title}',
            '${subreport}',
            '${subreport.id}: ${subreport.text}. ',
            '${/subreport}',
        );
        foreach ($documentElements as $documentElement) {
            $section->addText($documentElement);
        }

        $objWriter = IOFactory::createWriter($phpWord);
        $templatePath = 'test.docx';
        $objWriter->save($templatePath);

        // replace placeholders and save the file
        $templateProcessor = new TemplateProcessor($templatePath);
        $templateProcessor->setValue('title', 'Some title');
        $templateProcessor->cloneBlock('subreport', 2);
        $templateProcessor->setValue('subreport.id', '123', 1);
        $templateProcessor->setValue('subreport.text', 'Some text', 1);
        $templateProcessor->setValue('subreport.id', '456', 1);
        $templateProcessor->setValue('subreport.text', 'Some other text', 1);
        $templateProcessor->saveAs($templatePath);

        // assert the block has been cloned twice
        // and the placeholders have been replaced correctly
        $phpWord = IOFactory::load($templatePath);
        $sections = $phpWord->getSections();
        /** @var \PhpOffice\PhpWord\Element\TextRun[] $actualElements */
        $actualElements = $sections[0]->getElements();
        unlink($templatePath);
        $expectedElements = array(
            'Title: Some title',
            '123: Some text. ',
            '456: Some other text. ',
        );
        $this->assertCount(count($expectedElements), $actualElements);
        foreach ($expectedElements as $i => $expectedElement) {
            $this->assertEquals(
                $expectedElement,
                $actualElements[$i]->getElement(0)->getText()
            );
        }
    }

    /**
     * Template macros can be fixed.
     *
     * @covers ::fixBrokenMacros
     * @test
     */
    public function testFixBrokenMacros()
    {
        $templateProcessor = new TestableTemplateProcesor();

        $fixed = $templateProcessor->fixBrokenMacros('<w:r><w:t>normal text</w:t></w:r>');
        $this->assertEquals('<w:r><w:t>normal text</w:t></w:r>', $fixed);

        $fixed = $templateProcessor->fixBrokenMacros('<w:r><w:t>${documentContent}</w:t></w:r>');
        $this->assertEquals('<w:r><w:t>${documentContent}</w:t></w:r>', $fixed);

        $fixed = $templateProcessor->fixBrokenMacros('<w:r><w:t>$</w:t><w:t>{documentContent}</w:t></w:r>');
        $this->assertEquals('<w:r><w:t>${documentContent}</w:t></w:r>', $fixed);

        $fixed = $templateProcessor->fixBrokenMacros('<w:r><w:t>$1500</w:t><w:t>${documentContent}</w:t></w:r>');
        $this->assertEquals('<w:r><w:t>$1500</w:t><w:t>${documentContent}</w:t></w:r>', $fixed);

        $fixed = $templateProcessor->fixBrokenMacros('<w:r><w:t>$1500</w:t><w:t>$</w:t><w:t>{documentContent}</w:t></w:r>');
        $this->assertEquals('<w:r><w:t>$1500</w:t><w:t>${documentContent}</w:t></w:r>', $fixed);

        $fixed = $templateProcessor->fixBrokenMacros('<w:r><w:t>25$ plus some info {hint}</w:t></w:r>');
        $this->assertEquals('<w:r><w:t>25$ plus some info {hint}</w:t></w:r>', $fixed);

        $fixed = $templateProcessor->fixBrokenMacros('<w:t>$</w:t></w:r><w:bookmarkStart w:id="0" w:name="_GoBack"/><w:bookmarkEnd w:id="0"/><w:r><w:t xml:space="preserve">15,000.00. </w:t></w:r><w:r w:rsidR="0056499B"><w:t>$</w:t></w:r><w:r w:rsidR="00573DFD" w:rsidRPr="00573DFD"><w:rPr><w:iCs/></w:rPr><w:t>{</w:t></w:r><w:proofErr w:type="spellStart"/><w:r w:rsidR="00573DFD" w:rsidRPr="00573DFD"><w:rPr><w:iCs/></w:rPr><w:t>variable_name</w:t></w:r><w:proofErr w:type="spellEnd"/><w:r w:rsidR="00573DFD" w:rsidRPr="00573DFD"><w:rPr><w:iCs/></w:rPr><w:t>}</w:t></w:r>');
        $this->assertEquals('<w:t>$</w:t></w:r><w:bookmarkStart w:id="0" w:name="_GoBack"/><w:bookmarkEnd w:id="0"/><w:r><w:t xml:space="preserve">15,000.00. </w:t></w:r><w:r w:rsidR="0056499B"><w:t>${variable_name}</w:t></w:r>', $fixed);
    }

    public function testMainPartNameDetection()
    {
        $templateProcessor = new TemplateProcessor(__DIR__ . '/_files/templates/document22-xml.docx');

        $variables = array('test');

        $this->assertEquals($variables, $templateProcessor->getVariables());
    }
}
