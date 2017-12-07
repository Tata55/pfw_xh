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
use Pfw\View\View;

class InfoControllerTest extends TestCase
{
    /**
     * @return void
     */
    public function testDefaultAction()
    {
        global $pth;

        $pth = ['folder' => ['plugins' => './plugins/']];
        $systemCheckServiceMock = $this->createMock(SystemCheckService::class);
        $systemCheckServiceMock->expects($this->any())->method('minPhpVersion')->willReturn($systemCheckServiceMock);
        $systemCheckServiceMock->expects($this->any())->method('minXhVersion')->willReturn($systemCheckServiceMock);
        $systemCheckServiceMock->expects($this->any())->method('writable')->willReturn($systemCheckServiceMock);
        $systemCheckServiceMock->expects($this->once())->method('getChecks')->willReturn([]);
        $scscreatemock = $this->mockStaticMethod(SystemCheckService::class, 'create');
        $scscreatemock->expects($this->any())->willReturn($systemCheckServiceMock);
        $viewMock = $this->createMock(View::class);
        $viewMock->expects($this->once())->method('template')->with('info')->willReturn($viewMock);
        $viewMock->expects($this->once())->method('data')->with([
            'logo' => './plugins/pfw/pfw.png',
            'version' => '@PLUGIN_VERSION@',
            'checks' => []
        ])->willReturn($viewMock);
        $viewMock->expects($this->once())->method('render');
        $viewcreatemock = $this->mockStaticMethod(View::class, 'create');
        $viewcreatemock->expects($this->any())->willReturn($viewMock);
        (new InfoController)->defaultAction();
        $viewcreatemock->restore();
        $scscreatemock->restore();
    }
}
