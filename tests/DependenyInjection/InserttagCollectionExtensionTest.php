<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\Test\DependencyInjection;

use HeimrichHannot\ContaoInserttagCollectionBundle\DependencyInjection\InserttagCollectionExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class InserttagCollectionExtensionTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $container = new ContainerBuilder(new ParameterBag(['kernel.debug' => false]));
        $extension = new InserttagCollectionExtension();
        $extension->load([], $container);
    }

    /**
     * Tests the object instantiation.
     */
    public function testCanBeInstantiated()
    {
        $extension = new InserttagCollectionExtension();
        $this->assertInstanceOf(InserttagCollectionExtension::class, $extension);
    }
}
