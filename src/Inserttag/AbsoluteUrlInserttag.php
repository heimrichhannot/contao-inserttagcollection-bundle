<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoInserttagCollectionBundle\Inserttag;


use Contao\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AbsoluteUrlInserttag
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function replaceInsertTagsAbsolute($strTag)
    {
        $arrSplit = explode('::', $strTag);

        if ($arrSplit[0] == 'link_url_abs')
        {
            if (isset($arrSplit[1]))
            {
                $pageModel = $this->container->get('huh.utils.url')->getJumpToPageObject($arrSplit[1]);
                $url = $this->container->get('contao.routing.url_generator')->generate($pageModel->alias, $pageModel->row());
                return $url;

//
//                Controller::generateFrontendUrl()
//                $this->container->get('contao')->
//                return Url::generateAbsoluteUrl($arrSplit[1]);
            }

            return '';
        }

        return false;
    }
}