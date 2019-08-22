<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\Test;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\ContaoInserttagCollectionBundle\HeimrichHannotContaoInserttagCollectionBundle;

class HeimrichHannotContaoInserttagCollectionBundleTest extends ContaoTestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new HeimrichHannotContaoInserttagCollectionBundle();
        $this->assertInstanceOf(HeimrichHannotContaoInserttagCollectionBundle::class, $bundle);
    }
}
