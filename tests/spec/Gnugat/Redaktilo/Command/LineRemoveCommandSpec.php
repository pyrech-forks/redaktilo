<?php

/*
 * This file is part of the Redaktilo project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Gnugat\Redaktilo\Command;

use Gnugat\Redaktilo\Converter\LineContentConverter;
use Gnugat\Redaktilo\File;
use PhpSpec\ObjectBehavior;

class LineRemoveCommandSpec extends ObjectBehavior
{
    const ORIGINAL_FILENAME = '%s/tests/fixtures/sources/life-of-brian.txt';

    private $rootPath;
    private $converter;

    function let(File $file, LineContentConverter $converter)
    {
        $this->rootPath = __DIR__.'/../../../../../';
        $this->converter = $converter;

        $filename = sprintf(self::ORIGINAL_FILENAME, $this->rootPath);
        $lines = file($filename, FILE_IGNORE_NEW_LINES);

        $this->converter->from($file)->willReturn($lines);
        $this->beConstructedWith($this->converter);
    }

    function it_is_a_command()
    {
        $this->shouldImplement('Gnugat\Redaktilo\Command\Command');
    }

    function it_removes_lines(File $file)
    {
        $expectedFilename = sprintf(self::ORIGINAL_FILENAME, $this->rootPath);
        $expectedLines = file($expectedFilename, FILE_IGNORE_NEW_LINES);

        $lineNumber = 2;

        unset($expectedLines[$lineNumber]);

        $input = array(
            'file' => $file,
            'location' => $lineNumber
        );

        $this->converter->back($file, $expectedLines)->shouldBeCalled();
        $this->execute($input);
    }
} 