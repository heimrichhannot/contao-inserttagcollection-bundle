<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoInserttagCollectionBundle\Test;


use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\ContaoInserttagCollectionBundle\DependencyInjection\InserttagCollectionExtension;
use HeimrichHannot\ContaoInserttagCollectionBundle\HeimrichHannotContaoInserttagCollectionBundle;

class HeimrichHannotContaoInserttagCollectionBundleTest extends ContaoTestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new HeimrichHannotContaoInserttagCollectionBundle();
        $this->assertInstanceOf(HeimrichHannotContaoInserttagCollectionBundle::class, $bundle);
    }

    public function testGetContainerExtension()
    {
        $bundle = new HeimrichHannotContaoInserttagCollectionBundle();
        $this->assertInstanceOf(InserttagCollectionExtension::class, $bundle->getContainerExtension());
    }
}