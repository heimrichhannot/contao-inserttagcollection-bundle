<?php

$GLOBALS['TL_HOOKS']['replaceInsertTags']['huhInserttagCollection'] = [\HeimrichHannot\ContaoInserttagCollectionBundle\EventListener\InserttagListener::class, 'onReplaceInsertTags'];