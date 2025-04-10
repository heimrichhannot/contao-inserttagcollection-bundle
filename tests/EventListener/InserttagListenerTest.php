<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\Test\EventListener;

use Contao\StringUtil;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\ContaoInserttagCollectionBundle\EventListener\InserttagListener;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InserttagListenerTest extends ContaoTestCase
{
    public function createInsertTagListener(): InserttagListener
    {
        $stringUtilMock = $this->mockAdapter(['encodeEmail']);
        $stringUtilMock->method('encodeEmail')->willReturnArgument(0);

        $framework = $this->mockContaoFramework([
            StringUtil::class => $stringUtilMock,
        ]);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $utils = $this->createMock(Utils::class);

        return new InserttagListener($framework, $urlGenerator, $utils);
    }

    public function testReplaceInserttags()
    {
        $listener = $this->createInsertTagListener();

        $this->assertSame('<small>', $listener->onReplaceInsertTags('{{small}}'));
        $this->assertSame('</small>', $listener->onReplaceInsertTags('{{endsmall}}'));
    }

    // public function testAbsoluteUrl()
    // {
    //     $container = $this->mockContainer();
    //
    //     $urlUtil = $this->createMock(UrlUtil::class);
    //     $urlUtil->method('getJumpToPageObject')->willReturnCallback(function ($pageId) {
    //         switch ($pageId) {
    //             case 0:
    //                 return null;
    //             case 1:
    //                 $page = new \stdClass();
    //                 $page->id = 1;
    //                 $page->alias = 'index';
    //
    //                 return $page;
    //         }
    //     });
    //     $container->set('huh.utils.url', $urlUtil);
    //
    //     $routerMock = $this->createMock(UrlGenerator::class);
    //     $routerMock->method('generate')->willReturnCallback(function ($alias, $parameter, $referenceType) {
    //         if ('index' === $alias) {
    //             return 'https://example.org/de/';
    //         }
    //
    //         return 'https://example.org/de/'.$alias.'/';
    //     });
    //     $container->set('contao.routing.url_generator', $routerMock);
    //
    //     $listener = new InserttagListener($container, $this->mockContaoFramework());
    //     $this->assertEmpty($listener->generateLinkAbsoluteUrl([]));
    //     $this->assertEmpty($listener->generateLinkAbsoluteUrl(['link_url_abs']));
    //
    //     $this->assertEmpty($listener->generateLinkAbsoluteUrl(['link_url_abs', 0]));
    //     $this->assertSame('https://example.org/de/', $listener->generateLinkAbsoluteUrl(['link_url_abs', 1]));
    // }

    public function testEmailLabel()
    {
        $listener = $this->createMock(InserttagListener::class);

        $this->assertEmpty($listener->generateEmailLabel(['link_url_abs']));
        $this->assertEmpty($listener->generateEmailLabel(['link_url_abs', 'halloWelt']));
        $this->assertEmpty($listener->generateEmailLabel(['link_url_abs', 'hallo@Welt']));
        $this->assertEmpty($listener->generateEmailLabel(['link_url_abs', '<script>console.log(\'hacked\');</script>']));

        $this->assertSame('<a href="mailto:info@example.org">info@example.org</a>', $listener->generateEmailLabel(['link_url_abs', 'info@example.org']));
        $this->assertSame('<a href="mailto:info@example.org">E-Mail</a>', $listener->generateEmailLabel(['link_url_abs', 'info@example.org', 'E-Mail']));
        $this->assertSame(
            '<a href="mailto:info@example.org" class="btn btn-default">E-Mail</a>',
            $listener->generateEmailLabel(['link_url_abs', 'info@example.org', 'E-Mail', 'btn btn-default']),
        );
        $this->assertSame(
            '<a id="link" href="mailto:info@example.org" class="btn btn-default">E-Mail</a>',
            $listener->generateEmailLabel(['link_url_abs', 'info@example.org', 'E-Mail', 'btn btn-default', 'link']),
        );
        $this->assertSame(
            '<a id="link" href="mailto:info@example.org">E-Mail</a>',
            $listener->generateEmailLabel(['link_url_abs', 'info@example.org', 'E-Mail', '', 'link']),
        );
        $this->assertSame(
            '<a id="link" href="mailto:info@example.org">info@example.org</a>',
            $listener->generateEmailLabel(['link_url_abs', 'info@example.org', '', '', 'link']),
        );
    }

    public function testSmallStartTag()
    {
        $listener = $this->createInsertTagListener();

        $this->assertSame('<small>', $listener->generateSmallStartTag(['small']));
    }

    public function testSmallEndTag()
    {
        $listener = $this->createInsertTagListener();

        $this->assertSame('</small>', $listener->generateSmallEndTag(['endsmall']));
    }
}
