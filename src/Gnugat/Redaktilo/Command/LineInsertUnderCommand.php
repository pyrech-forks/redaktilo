<?php

/*
 * This file is part of the Redaktilo project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gnugat\Redaktilo\Command;

/**
 * Inserts the given addition in the given file under the given location.
 */
class LineInsertUnderCommand implements Command
{
    /** {@inheritdoc} */
    public function execute(array $input)
    {
        $file = $input['file'];
        $location = 1 + (isset($input['location']) ? $input['location'] : $file->getCurrentLineNumber());
        $addition = $input['addition'];

        $lines = $file->getLines();
        array_splice($lines, $location, 0, $addition);
        $file->setLines($lines);

        $file->setCurrentLineNumber($location);
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return 'insert_under';
    }
}
