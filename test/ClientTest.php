<?php
/**
 * Copyright 2016-2021 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Cache
 */

namespace Horde\Mongo\Test;

use Horde\Mongo\Client;
use PHPUnit\Framework\TestCase;

/**
 * A basic testcase for CLI
 *
 * @author   Ralf Lang <lang@b1-systems.de>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Cache
 */
class NullClientTest extends TestCase
{
    public function setUp(): void
    {
        $this->nullClient = new Client('mongodb://127.0.01');
    }

    public function testGetsConstructed()
    {
        $this->assertInstanceOf(Client::class, $this->nullClient);
    }
}
