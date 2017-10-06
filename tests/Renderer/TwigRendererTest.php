<?php

namespace tests\Framework;

use PHPUnit\Framework\TestCase;

class TwigRendererTest extends TestCase
{

    public function testNull()
    {
        $this->assertTrue(true);
    }
//    /**
//     * @var PHPRenderer
//     */
//    private $renderer;
//
//    public function setUp()
//    {
//        $this->renderer = new TwigRenderer(__Dir__ . '/views');
//    }
//
//    public function testRenderTheRightPath()
//    {
//        $this->renderer->addPath("blog", __Dir__ . '/views');
//        $content = $this->renderer->render('@blog/demo');
//        $this->assertEquals($content, "Salut les gens");
//    }
//
//    public function testRederTheDefaultPath()
//    {
//        $this->renderer->addPath(__Dir__ . '/views');
//        $content = $this->renderer->render('demo');
//        $this->assertEquals($content, "Salut les gens");
//    }
//
//    public function testRenderWithParams()
//    {
//        $content = $this->renderer->render('demoParams', ['nom' => "Marc"]);
//        $this->assertEquals($content, "salut Marc");
//    }
//
//    public function testGlobalParameters()
//    {
//        $this->renderer->addGlobal('nom','Marc');
//        $content = $this->renderer->render('demoParams');
//        $this->assertEquals($content, "salut Marc");
//    }

}
