<?php

/*
 * This file is part of the Redaktilo project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Gnugat\Redaktilo\Service;

use Gnugat\Redaktilo\File;
use Gnugat\Redaktilo\Service\ContentFactory;
use Gnugat\Redaktilo\Service\LineBreak;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class FilesystemSpec extends ObjectBehavior
{
    private $sourceFilename;
    private $copyFilename;

    private $fileCopier;

    function let(SymfonyFilesystem $symfonyFilesystem)
    {
        $this->sourceFilename = __DIR__.'/../../../../fixtures/sources/copy-me.txt';
        $this->copyFilename = __DIR__.'/../../../../fixtures/copies/edit-me.txt';

        $this->fileCopier = new SymfonyFilesystem();
        $lineBreak = new LineBreak();
        $contentFactory = new ContentFactory();

        $this->beConstructedWith($lineBreak, $symfonyFilesystem, $contentFactory);
    }

    function it_opens_existing_files()
    {
        $this->fileCopier->copy($this->sourceFilename, $this->copyFilename, true);

        $file = $this->open($this->copyFilename);

        $file->shouldHaveType('Gnugat\Redaktilo\File');
    }

    function it_cannot_open_new_files()
    {
        @unlink($this->copyFilename);

        $exception = '\Gnugat\Redaktilo\Exception\FileNotFoundException';
        $this->shouldThrow($exception)->duringOpen($this->copyFilename);
    }

    function it_creates_new_files()
    {
        @unlink($this->copyFilename);

        $file = $this->create($this->copyFilename);

        $file->shouldHaveType('Gnugat\Redaktilo\File');
    }

    function it_cannot_create_existing_files()
    {
        $this->fileCopier->copy($this->sourceFilename, $this->copyFilename, true);

        $exception = '\Gnugat\Redaktilo\Exception\IOException';
        $this->shouldThrow($exception)->duringCreate($this->copyFilename);
    }

    function it_detects_if_file_exists()
    {
        @unlink($this->copyFilename);

        $this->exists($this->copyFilename)->shouldBe(false);

        $this->fileCopier->copy($this->sourceFilename, $this->copyFilename, true);

        $this->exists($this->copyFilename)->shouldBe(true);
    }

    function it_writes_files(SymfonyFilesystem $symfonyFilesystem)
    {
        $this->fileCopier->copy($this->sourceFilename, $this->copyFilename, true);
        $content = file_get_contents($this->copyFilename);
        $lines = file($this->copyFilename);
        $file = new File($this->copyFilename, $lines);

        $symfonyFilesystem->dumpFile($this->copyFilename, $content, null)->shouldBeCalled();

        $this->write($file);
    }
}
