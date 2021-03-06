<?php

/*
 * Copyright 2017 Christoph M. Becker
 *
 * This file is part of Pfw_XH.
 *
 * Pfw_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pfw_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pfw_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Pfw;

use Pfw\TestCase;

class SystemCheckTest extends TestCase
{
    /**
     * @var SystemCheck
     */
    private $subject;

    /**
     * @return void
     */
    protected function setUp()
    {
        global $plugin_tx;

        $plugin_tx = ['pfw' => ['syscheck_success' => 'okay']];
        $this->subject = new SystemCheck('foobar', SystemCheck::SUCCESS);
    }

    /**
     * @return void
     */
    public function testGetState()
    {
        $this->assertEquals(SystemCheck::SUCCESS, $this->subject->getState());
    }

    /**
     * @return void
     */
    public function testGetLabel()
    {
        $this->assertEquals('foobar', $this->subject->getLabel());
    }

    /**
     * @return void
     */
    public function testGetStateLabel()
    {
        $this->assertEquals('okay', $this->subject->getStateLabel());
    }
}
