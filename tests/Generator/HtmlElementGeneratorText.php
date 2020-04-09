<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\Test\Generator;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\ContaoInserttagCollectionBundle\Generator\HtmlElementGenerator;

class HtmlElementGeneratorText extends ContaoTestCase
{
    public function testGenerateStartTag()
    {
        $this->assertSame('<span>', HtmlElementGenerator::generateStartTag('span'));
        $this->assertSame('<span>', HtmlElementGenerator::generateStartTag('span', null));
        $this->assertSame('<span>', HtmlElementGenerator::generateStartTag('span', ''));

        $this->assertSame('<span class="hello">', HtmlElementGenerator::generateStartTag('span', 'class=hello'));
        $this->assertSame('<span class="hello world">', HtmlElementGenerator::generateStartTag('span', 'class=hello world'));
        $this->assertSame('<span id="unique">', HtmlElementGenerator::generateStartTag('span', 'id=unique'));
        $this->assertSame('<span class="hello world" id="unique">', HtmlElementGenerator::generateStartTag('span', 'class=hello world&id=unique'));
        $this->assertSame('<span class="hello world" id="unique" title="Hallo Welt">', HtmlElementGenerator::generateStartTag('span', 'class=hello world&id=unique&title=Hallo Welt'));
    }

    public function testGenerateEndTag()
    {
        $this->assertSame('</span>', HtmlElementGenerator::generateEndTag('span'));
        $this->assertSame('</a>', HtmlElementGenerator::generateEndTag('a'));
    }
}
