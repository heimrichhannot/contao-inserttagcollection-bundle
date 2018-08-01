<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoInserttagCollectionBundle\EventListener;


use Contao\ContentDownload;
use Contao\ContentModel;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Validator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

class InserttagListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function replaceInserttags(string $tag)
    {
        $tag = explode('::', $tag);
        if (empty($tag) || count($tag) < 2)
        {
            return false;
        }
        switch ($tag[0])
        {
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
        if (isset($tag[1]))
        {
            $pageModel = $this->container->get('huh.utils.url')->getJumpToPageObject($tag[1]);
            if ($pageModel)
            {
                $url = $this->container->get('contao.routing.url_generator')->generate($pageModel->alias, [], UrlGenerator::ABSOLUTE_URL);
                return $url;
            }
        }
        return '';
    }

    public function emailLabel(array $tag)
    {
        $email = StringUtil::encodeEmail($tag[1]);

        if (empty($email))
        {
            return false;
        }

        // label parameters
        $label   = (isset($tag[2]) && !empty($tag[2])) ? $tag[2] : preg_replace('/\?.*$/', '', $email);
        $classes = (isset($tag[3]) && !empty($tag[3])) ? $tag[3] . ' ' : '';
        $id      = (isset($tag[4]) && !empty($tag[4])) ? $tag[4] : '';

        $link = sprintf('<a id="%s" href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;%s" class="%s">%s</a>', $id, $email, $classes, $label);

        return $link;
    }

    private function generateDownload(array $tag)
    {
        $source = strip_tags(($tag[1])); // remove <span> etc, otherwise Validator::isuuid fail

        $file = null;
        if (Validator::isUuid($source))
        {
            $file = FilesModel::findByUuid($source);
        }
        elseif (($pos = strpos($source, '/')) !== false) {
            if (0 === $pos)
            {
                $source = ltrim($source, "/");
            }
                $file = FilesModel::findByPath($source);
        }
        if ($file || $file->uuid)
        {
            $source = StringUtil::binToUuid($file->uuid);
        }

        $downloadDate = new ContentModel();
        $downloadDate->customTpl = 'ce_download_inserttag';
        $downloadDate->singleSRC = $source;
        $downloadDate->linkTitle = strip_tags($tag[2]); // remove <span> etc
        $downloadDate->cssID[1] = 'inserttag_download ' . strip_tags($tag[3]);
        $downloadDate->cssID[0] = strip_tags($tag[4]);

        return new ContentDownload($downloadDate);
    }

    public function download(array $tag)
    {
        $download = $this->generateDownload($tag);
        if (isset($tag[3]) && !empty($tag[3]))
        {
            $download->linkTitle = $tag[3];
        }
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


}