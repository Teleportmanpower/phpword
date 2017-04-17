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
 * @link        https://github.com/PHPOffice/PHPWord
 * @copyright   2010-2014 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Tests\Reader;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Reader\MsDoc;

/**
 * Test class for PhpOffice\PhpWord\Reader\MsDoc
 *
 * @coversDefaultClass \PhpOffice\PhpWord\Reader\MsDoc
 * @runTestsInSeparateProcesses
 */
class MsDocTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test canRead() method
     */
    public function testCanRead()
    {
        $object = new MsDoc();
        $filename = __DIR__ . '/../_files/documents/reader.doc';
        $this->assertTrue($object->canRead($filename));
    }

    /**
     * Can read exception
     */
    public function testCanReadFailed()
    {
        $object = new MsDoc();
        $filename = __DIR__ . '/../_files/documents/foo.doc';
        $this->assertFalse($object->canRead($filename));
    }

    /**
     * Load
     */
    public function testLoad()
    {
        $filename = __DIR__ . '/../_files/documents/reader.doc';
        $phpWord = IOFactory::load($filename, 'MsDoc');
        $this->assertInstanceOf('PhpOffice\\PhpWord\\PhpWord', $phpWord);
    }
}
