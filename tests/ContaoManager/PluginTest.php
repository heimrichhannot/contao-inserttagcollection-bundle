<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\Test\Plugin;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\DelegatingParser;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\ContaoInserttagCollectionBundle\ContaoManager\Plugin;
use HeimrichHannot\ContaoInserttagCollectionBundle\HeimrichHannotContaoInserttagCollectionBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class PluginTest extends ContaoTestCase
{
    public function testInstantiation()
    {
        static::assertInstanceOf(Plugin::class, new Plugin());
    }

    public function testGetBundles()
    {
        $plugin = new Plugin();

        /** @var BundleConfig[] $bundles */
        $bundles = $plugin->getBundles(new DelegatingParser());

        $this->assertCount(1, $bundles);
        $this->assertInstanceOf(BundleConfig::class, $bundles[0]);
        $this->assertSame(HeimrichHannotContaoInserttagCollectionBundle::class, $bundles[0]->getName());
        $this->assertSame([ContaoCoreBundle::class], $bundles[0]->getLoadAfter());
    }

    public function testRegisterContainerConfiguration()
    {
        $plugin = new Plugin();
        $loader = $this->createMock(LoaderInterface::class);
        $loader->expects($this->once())->method('load');
        $plugin->registerContainerConfiguration($loader, []);
    }

    public function testGetExtensionConfig()
    {
        $plugin = new Plugin();
        $container = $this->createMock(ContainerBuilder::class);
        $this->assertEmpty($plugin->getExtensionConfig('test', [], $container));

        $this->assertArrayHasKey('huh_amp', $plugin->getExtensionConfig('huh_amp', [], $container));
    }
}
