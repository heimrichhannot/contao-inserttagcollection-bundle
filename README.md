# Contao Inserttags Collection Bundle

This bundle provides some additional inserttags for contao CMS.

## Inserttags

Insertag      | Example              | Description
--------------|----------------------|-------------
link_url_abs  | {{link_url_abs::92}} | Get the absolute url of an page.
email_label   | {{email_label::info@example.org::E-Mail}} | Generate an e-mail link with custom label. Custom classes and id are also possile: ({{email_label::info@example.org::E-Mail::btn btn-default::my_custom_email_link}}
download      | {{download::9263228b-9577-11e8-abd4-a08cfddc0261}} | Generate an download link to the file with file name as label and download size. File parameter can be file uuid or file path. Optional third parameter for custom label.
download_link | {{download_link::9263228b-9577-11e8-abd4-a08cfddc0261}} | Get the download url. File parameter can be file uuid or file path.
download_size | {{download_size::9263228b-9577-11e8-abd4-a08cfddc0261}} | Get the  formatted download size. File parameter can be file uuid or file path.