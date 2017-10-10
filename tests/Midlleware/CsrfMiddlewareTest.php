<?php

namespace Tests\Framework\Middleware;

use Framework\Exceptions\CsrfInvalidException;
use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;

class CsrfMiddlewareTest extends TestCase
{
    /**
     * @var CsrfMiddleware
     */
    private $middleware;
    private $session;

    public function setUp()
    {
        $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session);
    }

    public function testgetRequestPass()
    {
        $delegate = $this->getMockBuilder(DelegateInterface::class)
            ->setMethods(['process'])
            ->getMock();

        $delegate->expects($this->once())
            ->method('process')
            ->willReturn(new Response());

        $request = (new ServerRequest('GET', 'demo'));
        $this->middleware->process($request, $delegate);
    }

    public function testBlockPostRequestWithoutCsrf()
    {
        $delegate = $this->getMockBuilder(DelegateInterface::class)
            ->setMethods(['process'])
            ->getMock();

        $delegate->expects($this->never())
            ->method('process')
            ->willReturn(new Response());


        $request = (new ServerRequest('POST', 'demo'));
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $delegate);
    }

    public function testBlockPostRequestWithInvalidCsrf()
    {
        $delegate = $this->getMockBuilder(DelegateInterface::class)
            ->setMethods(['process'])
            ->getMock();

        $delegate->expects($this->never())
            ->method('process')
            ->willReturn(new Response());

        $request = (new ServerRequest('POST', 'demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => "aagage"]);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $delegate);
    }

    public function testPostRequestWithTokenPass()
    {
        $delegate = $this->getMockBuilder(DelegateInterface::class)
            ->setMethods(['process'])
            ->getMock();

        $delegate->expects($this->once())
            ->method('process')
            ->willReturn(new Response());

        $request = (new ServerRequest('POST', 'demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $delegate);
    }


    public function testLimitTheTokenNumber()
    {
        for ($i = 0; $i < 100; ++$i) {
            $token = $this->middleware->generateToken();
        }
        $this->assertCount(50, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][49]);
    }
}
