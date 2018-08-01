<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle;

use HeimrichHannot\ContaoInserttagCollectionBundle\DependencyInjection\InserttagCollectionExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoInserttagCollectionBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new InserttagCollectionExtension();
    }
}
