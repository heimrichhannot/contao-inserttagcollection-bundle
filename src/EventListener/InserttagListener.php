<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\EventListener;

use Contao\ContentDownload;
use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\CoreBundle\Routing\UrlGenerator;
use Contao\Database;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Validator;
use HeimrichHannot\ContaoInserttagCollectionBundle\Generator\HtmlElementGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InserttagListener
{
    /**
     * @var array
     */
    protected $contentElementUniqueIdCache = [];
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

    /**
     * @return bool|string
     */
    public function onReplaceInsertTags(string $tag)
    {
        $tag = trim($tag, '{}');
        $tag = explode('::', $tag);

        if (empty($tag)) {
            return false;
        }

        switch ($tag[0]) {
            case 'link_url_abs':
                return $this->generateLinkAbsoluteUrl($tag);

            case 'email_label':
                return $this->generateEmailLabel($tag);

            case 'download':
                return $this->generateDownload($tag);

            case 'download_link':
                return $this->generateDownloadLink($tag);

            case 'download_size':
                return $this->generateDownloadSize($tag);

            case 'small':
                return $this->generateSmallStartTag($tag);

            case 'endsmall':
                return $this->generateSmallEndTag($tag);

            case 'strtotime':
                return $this->generateStrToTime($tag);

            case 'span':
                return $this->generateHtmlTag('span', $tag);

            case 'endspan':
                return $this->generateHtmlEndTag('span', $tag);
        }

        return false;
    }

    public function generateHtmlTag($htmlTag, $insertTag)
    {
        return HtmlElementGenerator::generateStartTag($htmlTag, isset($insertTag[1]) ? $insertTag[1] : null);
    }

    public function generateHtmlEndTag($htmlTag, $insertTag)
    {
        return HtmlElementGenerator::generateEndTag($htmlTag);
    }

    public function generateLinkAbsoluteUrl(array $tag)
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
     * @return string mailto-Link or empty string, if no valid mail adress given
     */
    public function generateEmailLabel(array $tag)
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

    public function generateDownload(array $tag)
    {
        $download = $this->doGenerateDownload($tag);

        return $download->generate();
    }

    public function generateDownloadLink(array $tag)
    {
        $download = $this->doGenerateDownload($tag);
        $download->generate();

        return $download->Template->href;
    }

    public function generateDownloadSize(array $tag)
    {
        $download = $this->doGenerateDownload($tag);
        $download->generate();

        return $download->Template->filesize;
    }

    public function generateSmallStartTag($tag)
    {
        return '<small>';
    }

    public function generateSmallEndTag($tag)
    {
        return '</small>';
    }

    public function generateStrToTime($tag)
    {
        $result = strtotime($tag[1], $tag[2] ?? time());

        if (isset($tag[3])) {
            $result = date($tag[3], $result);
        }

        return $result;
    }

    private function doGenerateDownload(array $tag)
    {
        $source = strip_tags($tag[1]); // remove <span> etc, otherwise Validator::isuuid fail

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

        $db = Database::getInstance();
        $id = $db->getNextId('tl_content');
        ++$id;

        while (!$db->isUniqueValue('tl_content', 'id', $id) || \in_array($id, $this->contentElementUniqueIdCache)) {
            ++$id;
        }
        $this->contentElementUniqueIdCache[] = $id;

        $downloadData = $this->framework->createInstance(ContentModel::class);
        $downloadData->id = $id;
        $downloadData->tstamp = time();
        $downloadData->type = 'download';
        $downloadData->customTpl = 'ce_download_inserttag';
        $downloadData->singleSRC = $source;

        if (isset($tag[2]) && \is_string($tag[2])) {
            $downloadData->linkTitle = $tag[2];
        }
        $cssClass = 'inserttag_download';
        $cssId = '';

        if (isset($tag[3]) && \is_string($tag[3])) {
            $cssClass .= ' '.strip_tags($tag[3]);
        }

        if (isset($tag[4]) && \is_string($tag[4])) {
            $cssId = strip_tags($tag[4]);
        }
        $downloadData->cssID = [$cssId, $cssClass];

        return $this->framework->createInstance(ContentDownload::class, [$downloadData]);
    }
}
