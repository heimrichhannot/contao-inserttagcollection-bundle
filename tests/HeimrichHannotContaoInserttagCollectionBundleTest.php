<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
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
