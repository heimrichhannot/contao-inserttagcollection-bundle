<?php

/*
 * @copyright Heimrich & Hannot GmbH (c) 2025
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\EventListener;

use Contao\ContentDownload;
use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\FilesModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Validator;
use HeimrichHannot\ContaoInserttagCollectionBundle\Generator\HtmlElementGenerator;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsHook('replaceInsertTags')]
class InserttagListener
{
    protected array $ceUniqueIdCache = [];

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Utils $utils,
    ) {}

    public function __invoke(string $tag): bool|string
    {
        $tag = \trim($tag, '{}');
        $tag = \explode('::', $tag);

        if (!$tagName = $tag[0] ?? null) {
            return false;
        }

        return match ($tagName)
        {
            'link_url_abs' => $this->generateLinkAbsoluteUrl($tag),
            'email_label' => $this->generateEmailLabel($tag),
            'download' => $this->generateDownload($tag),
            'download_link' => $this->generateDownloadLink($tag),
            'download_size' => $this->generateDownloadSize($tag),
            'small' => $this->generateSmallStartTag($tag),
            'endsmall' => $this->generateSmallEndTag($tag),
            'strtotime' => $this->generateStrToTime($tag),
            'span' => $this->generateHtmlTag('span', $tag),
            'endspan' => $this->generateHtmlEndTag('span', $tag),
            default => false,
        };
    }

    public function generateHtmlTag($htmlTag, $insertTag): false|string
    {
        return HtmlElementGenerator::generateStartTag($htmlTag, $insertTag[1] ?? null);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function generateHtmlEndTag($htmlTag, $insertTag): false|string
    {
        return HtmlElementGenerator::generateEndTag($htmlTag);
    }

    public function generateLinkAbsoluteUrl(array $tag): string
    {
        if (!$jumpTo = (int) ($tag[1] ?? 0)) {
            return '';
        }

        global $objPage;

        $pageModel = $jumpTo === $objPage->id
            ? $objPage
            : $this->utils->model()->findModelInstanceByPk('tl_page', $jumpTo);

        if (!$pageModel instanceof PageModel) {
            return '';
        }

        return $this->urlGenerator->generate($pageModel->alias, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @return string mailto-Link or empty string, if no valid mail address given
     */
    public function generateEmailLabel(array $tag): string
    {
        $emailLabel = $tag[1] ?? null;

        if (!$emailLabel || !Validator::isEmail($emailLabel)) {
            return '';
        }

        $email = StringUtil::encodeEmail('mailto:' . $emailLabel);
        $emailLabel = StringUtil::encodeEmail($emailLabel);

        $label = ($tag[2] ?? null) ?: $emailLabel;
        $classes = ($tag[3] ?? null) ? " class=\"$tag[3]\"" : '';
        $id = ($tag[4] ?? null) ? " id=\"$tag[4]\"" : '';

        return \sprintf('<a%s %s href="%s">%s</a>', $id, $classes, $email, $label);
    }

    public function generateDownload(array $tag): string
    {
        $download = $this->doGenerateDownload($tag);

        return $download->generate();
    }

    public function generateDownloadLink(array $tag): string
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

    public function generateSmallStartTag($tag): string
    {
        return '<small>';
    }

    public function generateSmallEndTag($tag): string
    {
        return '</small>';
    }

    public function generateStrToTime($tag): false|int|string
    {
        $result = strtotime($tag[1], $tag[2] ?? time());

        if (isset($tag[3])) {
            $result = date($tag[3], $result);
        }

        return $result;
    }

    private function doGenerateDownload(array $tag): ContentDownload
    {
        $source = \strip_tags($tag[1]); // remove <span> etc, otherwise Validator::isUuid fail

        /** @var ?FilesModel $file */
        $file = null;

        if (Validator::isUuid($source))
        {
            $file = $this->framework->getAdapter(FilesModel::class)->findByUuid($source);
        }
        elseif (false !== ($pos = strpos($source, '/')))
        {
            if (0 === $pos) {
                $source = ltrim($source, '/');
            }
            $file = $this->framework->getAdapter(FilesModel::class)->findByPath($source);
        }

        if ($file && $file->uuid) {
            $source = StringUtil::binToUuid($file->uuid);
        }

        $id = $this->getNextUniqueId();

        $dlContentModel = $this->framework->createInstance(ContentModel::class);
        $dlContentModel->id = $id;
        $dlContentModel->tstamp = time();
        $dlContentModel->type = 'download';
        $dlContentModel->customTpl = 'ce_download_inserttag';
        $dlContentModel->singleSRC = $source;

        if (\is_string($tag[2] ?? null)) {
            $dlContentModel->linkTitle = $tag[2];
        }
        $cssClass = 'inserttag_download';
        $cssId = '';

        if (\is_string($tag[3] ?? null)) {
            $cssClass .= ' ' . \strip_tags($tag[3]);
        }

        if (\is_string($tag[4] ?? null)) {
            $cssId = \strip_tags($tag[4]);
        }
        $dlContentModel->cssID = [$cssId, $cssClass];

        return $this->framework->createInstance(ContentDownload::class, [$dlContentModel]);
    }

    public function getNextUniqueId(): int
    {
        /** @var Database $db */
        $db = $this->framework->createInstance(Database::class);
        $id = $db->getNextId('tl_content');

        do
        {
            $id++;
        }
        while (\in_array($id, $this->ceUniqueIdCache) || !$db->isUniqueValue('tl_content', 'id', $id));

        $this->ceUniqueIdCache[] = $id;

        return $id;
    }
}
