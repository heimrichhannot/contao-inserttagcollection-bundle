<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\Generator;

class HtmlElementGenerator
{
    /**
     * Generates an opening tag based on given parameters.
     *
     * @param string      $htmlTag         An html element tag name like span, div, small, ...
     * @param string|null $attributesQuery Element attributes in query form, example: "class=myclass 4&value=5&id=uniqeId"
     *
     * @return false|string
     */
    public static function generateStartTag(string $htmlTag, ?string $attributesQuery = null)
    {
        $dom = new \DomDocument('1.0', 'UTF-8');
        $node = $dom->createElement($htmlTag);
        if (!empty($attributesQuery)) {
            static::addElementAttributesFromQuery($node, $attributesQuery);
        }
        $dom->appendChild($node);
        $htmlContent = $dom->saveHTML($node);
        $openingTag = substr($htmlContent, 0, strpos($htmlContent, '</'));

        return $openingTag;
    }

    /**
     * Generates an end tag based on given parameter.
     *
     * @param string $htmlTag An html element tag name like span, div, small, ...
     *
     * @return false|string
     */
    public static function generateEndTag(string $htmlTag)
    {
        $dom = new \DomDocument('1.0', 'UTF-8');
        $node = $dom->createElement($htmlTag);
        $dom->appendChild($node);
        $htmlContent = $dom->saveHTML($node);
        $endTag = substr($htmlContent, strpos($htmlContent, '</'));

        return $endTag;
    }

    public static function addElementAttributesFromQuery(\DOMElement &$node, string $query)
    {
        $result = [];
        parse_str($query, $result);
        foreach ($result as $attribute => $value) {
            $result = $node->setAttribute($attribute, $value);
        }
    }
}
