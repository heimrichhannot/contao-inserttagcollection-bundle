<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\EventListener;

use Contao\ContentDownload;
use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\CoreBundle\Routing\UrlGenerator;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Validator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InserttagListener
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    public function __construct(ContainerInterface $container, ContaoFrameworkInterface $framework)
    {
        $this->container = $container;
        $this->framework = $framework;
    }

    public function replaceInserttags(string $tag)
    {
        $tag = trim($tag, '{}');
        $tag = explode('::', $tag);
        if (empty($tag) || count($tag) < 2) {
            return false;
        }
        switch ($tag[0]) {
            case 'link_url_abs':
                return $this->linkAbsoluteUrl($tag);
            case 'email_label':
                return $this->emailLabel($tag);
            case 'download':
                return $this->download($tag);
            case 'download_link':
                return $this->downloadLink($tag);
            case 'download_size':
                return $this->downloadSize($tag);
        }

        return false;
    }

    public function linkAbsoluteUrl(array $tag)
    {
        if (isset($tag[1])) {
            $pageModel = $this->container->get('huh.utils.url')->getJumpToPageObject($tag[1]);
            if ($pageModel) {
                $url = $this->container->get('contao.routing.url_generator')->generate($pageModel->alias, [], UrlGenerator::ABSOLUTE_URL);

                return $url;
            }
        }

        return '';
    }

    /**
     * @param array $tag
     *
     * @return string mailto-Link or empty string, if no valid mail adress given
     */
    public function emailLabel(array $tag)
    {
        if (!isset($tag[1]) || !Validator::isEmail($tag[1])) {
            return '';
        }
        $emailLabel = $tag[1];
        $email = $this->framework->getAdapter(StringUtil::class)->encodeEmail('mailto:'.$tag[1]);
        // label parameters
        $label = (isset($tag[2]) && !empty($tag[2])) ? $tag[2] : preg_replace('/\?.*$/', '', $emailLabel);
        $classes = (isset($tag[3]) && !empty($tag[3])) ? ' class="'.$tag[3].'"' : '';
        $id = (isset($tag[4]) && !empty($tag[4])) ? 'id="'.$tag[4].'" ' : '';

        $link = sprintf('<a %shref="%s"%s>%s</a>', $id, $email, $classes, $label);

        return $link;
    }

    public function download(array $tag)
    {
        $download = $this->generateDownload($tag);

        return $download->generate();
    }

    public function downloadLink(array $tag)
    {
        $download = $this->generateDownload($tag);
        $download->generate();

        return $download->Template->href;
    }

    public function downloadSize(array $tag)
    {
        $download = $this->generateDownload($tag);
        $download->generate();

        return $download->Template->filesize;
    }

    private function generateDownload(array $tag)
    {
        $source = strip_tags(($tag[1])); // remove <span> etc, otherwise Validator::isuuid fail

        $file = null;
        if (Validator::isUuid($source)) {
            /** @var FilesModel $file */
            $file = $this->framework->getAdapter(FilesModel::class)->findByUuid($source);
        } elseif (false !== ($pos = strpos($source, '/'))) {
            if (0 === $pos) {
                $source = ltrim($source, '/');
            }
            /** @var FilesModel $file */
            $file = $this->framework->getAdapter(FilesModel::class)->findByPath($source);
        }
        if ($file && $file->uuid) {
            $source = StringUtil::binToUuid($file->uuid);
        }

        $downloadData = $this->framework->createInstance(ContentModel::class);
        $downloadData->type = 'download';
        $downloadData->customTpl = 'ce_download_inserttag';
        $downloadData->singleSRC = $source;
        if (isset($tag[2]) && is_string($tag[2])) {
            $downloadData->linkTitle = $tag[2];
        }
        if (isset($tag[3]) && is_string($tag[3])) {
            $downloadData->cssID[1] = 'inserttag_download '.strip_tags($tag[3]);
        }
        if (isset($tag[4]) && is_string($tag[4])) {
            $downloadData->cssID[0] = strip_tags($tag[4]);
        }

        return $this->framework->createInstance(ContentDownload::class, [$downloadData]);
    }
}
