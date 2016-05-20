<?php

/*
Copyright 2016 Christoph M. Becker
 
This file is part of Pfw_XH.

Pfw_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Pfw_XH is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Pfw_XH.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace Pfw;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    private $plugin;

    private $subject;
    
    private $systemRequest;
    
    private $request;
    
    private $systemResponse;
    
    private $response;

    public function setUp()
    {
        global $pth;

        $pth = array(
            'folder' => array(
                'content' => 'foo/bar/baz/'
            )
        );
        $this->plugin = $this->getMockBuilder('Pfw\\Plugin')
            ->disableOriginalConstructor()
            ->getMock();
        $this->systemRequest = new \PHPUnit_Extensions_MockStaticMethod(
            'Pfw\\System::request',
            null
        );
        $this->request = $this->getMockBuilder('Pfw\\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->systemRequest->expects($this->any())->willReturn($this->request);
        $this->systemResponse = new \PHPUnit_Extensions_MockStaticMethod(
            'Pfw\\System::response',
            null
        );
        $this->response = $this->getMockBuilder('Pfw\\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $this->systemResponse->expects($this->any())->willReturn($this->response);
        $this->subject = $this->getMockBuilder('Pfw\\Controller')
            ->setConstructorArgs([$this->plugin])
            ->setMethods(null)
            ->getMock();
    }
    
    public function tearDown()
    {
        $this->systemRequest->restore();
        $this->systemResponse->restore();
    }

    public function testDispatcher()
    {
        $this->assertNull($this->subject->getDispatcher());
    }

    public function testPlugin()
    {
        $this->assertSame($this->plugin, $this->subject->plugin());
    }
    
    public function testContentFolder()
    {
        $this->assertEquals('foo/bar/baz/', $this->subject->contentFolder());
    }
    
    public function testSeeOtherCallsRedirect()
    {
        $this->response->expects($this->any())->method('redirect')
            ->with($this->equalTo('absolute'), $this->equalTo(303));
        $url = $this->getMockBuilder('Pfw\\Url')
            ->disableOriginalConstructor()
            ->getMock();
        $url->expects($this->once())->method('absolute')->willReturn('absolute');
        $this->subject->seeOther($url);
    }
    
    public function testUrl()
    {
        $url = $this->getMockBuilder('Pfw\\Url')
            ->disableOriginalConstructor()
            ->getMock();
        $url->expects($this->once())->method('with');
        $this->request->expects($this->once())->method('url')->willReturn($url);
        $this->subject->url('foo');
    }
}
