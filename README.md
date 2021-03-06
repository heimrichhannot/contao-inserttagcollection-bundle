# Contao Inserttags Collection Bundle
[![Latest Stable Version](https://poser.pugx.org/heimrichhannot/contao-inserttagcollection-bundle/v/stable)](https://packagist.org/packages/heimrichhannot/contao-inserttagcollection-bundle)
[![Total Downloads](https://poser.pugx.org/heimrichhannot/contao-inserttagcollection-bundle/downloads)](https://packagist.org/packages/heimrichhannot/contao-inserttagcollection-bundle)
![CI](https://github.com/heimrichhannot/contao-inserttagcollection-bundle/workflows/CI/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/heimrichhannot/contao-inserttagcollection-bundle/badge.svg?branch=master)](https://coveralls.io/github/heimrichhannot/contao-inserttagcollection-bundle?branch=master)

This bundle provides some additional inserttags for contao CMS.

The download inserttag template is already prepared for [AMP Bundle](https://github.com/heimrichhannot/contao-amp-bundle).

## Inserttags

### Functionality

Inserttag      | Example              | Description
--------------|----------------------|-------------
strtotime     | `{{strtotime::midnight}}` | Returns supported return values of strtotime() (see [php docs](https://www.php.net/manual/de/datetime.formats.relative.php)). Also possible: `{{strtotime::+ 1 day::<timestamp>::<date/time format>}}`
link_url_abs  | `{{link_url_abs::92}}` | Get the absolute url of an page.
email_label   | `{{email_label::info@example.org::E-Mail}}` | Generate an e-mail link with custom label. Custom classes and id are also possible: (`{{email_label::info@example.org::E-Mail::btn btn-default::my_custom_email_link}}`)
download      | `{{download::9263228b-9577-11e8-abd4-a08cfddc0261}}` | Generate an download link to the file with file name as label and download size. File parameter can be file uuid or file path. Optional parameter for custom label, link css class and link css id.
download_link | `{{download_link::9263228b-9577-11e8-abd4-a08cfddc0261}}` | Get the download url. File parameter can be file uuid or file path.
download_size | `{{download_size::9263228b-9577-11e8-abd4-a08cfddc0261}}` | Get the  formatted download size. File parameter can be file uuid or file path.


### Html-tags

Inserttag      | Example              | Description
--------------|----------------------|-------------
small         | `{{small}}` | Start small text (Outputs `<small>`)
endsmall      | `{{endsmall}}` | Stops small text (Outputs `</small>`)
span         | `{{span::class=product highlight&id=product5&title=Super Product}}` | Start span element text (Example outputs `<span class="product highlight" id="product5" title="Super Product">`)
endspan      | `{{endspan}}` | Stops small text (Outputs `</span>`)

## Technical introduction

### Install

```
composer require heimrichhannot/contao-inserttagcollection-bundle
```

### Upgrade from module

This bundle replaces following modules: 
* `heimrichhannot/contao-inserttags_absolute`
* `heimrichhannot/contao-inserttag_email`
* `heimrichhannot/contao-inserttag_download`

See [upgrade notices](docs/upgrade.md) for more information.




