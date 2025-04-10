<?php

/*
 * @copyright Heimrich & Hannot GmbH (c) 2025
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoInserttagCollectionBundle\Generator;

class HtmlElementGenerator
{
    /**
     * Generates an opening tag based on given parameters.
     *
     * @param string      $htmlTag         An HTML element tag name like span, div, small, ...
     * @param string|null $attributesQuery Element attributes in query form, example: "class=myclass 4&value=5&id=uniqeId"
     *
     * @return false|string
     */
    public static function generateStartTag(string $htmlTag, ?string $attributesQuery = null): false|string
    {
        try
        {
            $dom = new \DomDocument('1.0', 'UTF-8');
            $node = $dom->createElement($htmlTag);

            if (!empty($attributesQuery)) {
                static::addElementAttributesFromQuery($node, $attributesQuery);
            }

            $dom->appendChild($node);
            $htmlContent = $dom->saveHTML($node);
        }
        catch (\DOMException)
        {
            return false;
        }

        return \substr($htmlContent, 0, \strpos($htmlContent, '</'));
    }

    /**
     * Generates an end tag based on given parameter.
     *
     * @param string $htmlTag An HTML element tag name like span, div, small, ...
     *
     * @return false|string
     */
    public static function generateEndTag(string $htmlTag): false|string
    {
        try
        {
            $dom = new \DomDocument('1.0', 'UTF-8');
            $node = $dom->createElement($htmlTag);
            $dom->appendChild($node);
            $htmlContent = $dom->saveHTML($node);
        }
        catch (\DOMException)
        {
            return false;
        }

        return \substr($htmlContent, \strpos($htmlContent, '</'));
    }

    public static function addElementAttributesFromQuery(\DOMElement &$node, string $query): void
    {
        \parse_str($query, $result);

        foreach ($result as $attribute => $value) {
            $result = $node->setAttribute($attribute, $value);
        }
    }
}
