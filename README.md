# Embedassets
[![License](https://poser.pugx.org/t3brightside/embedassets/license)](LICENSE.txt)
[![Packagist](https://img.shields.io/packagist/v/t3brightside/embedassets.svg?style=flat)](https://packagist.org/packages/t3brightside/embedassets)
[![Downloads](https://poser.pugx.org/t3brightside/embedassets/downloads)](https://packagist.org/packages/t3brightside/embedassets)
[![Brightside](https://img.shields.io/badge/by-t3brightside.com-orange.svg?style=flat)](https://t3brightside.com)

**TYPO3 CMS extension for embed CSS and JS files in Fluid.**
Embeds CSS and JS into Fluid HTML output. Embed assets are minifed.

## System requirements

- TYPO3 Fluid

## Install

- `composer req t3brightside/embedassets` or from TYPO3 extension repository **[embedassets](https://extensions.typo3.org/extension/embedassets/)**

## Use

Add namespace and `embed="1"` as in given examples
```xml
{namespace ea=Brightside\Embedassets\ViewHelpers}
<ea:asset.css embed="1" identifier="myindentifier" href="EXT:myextension/Resources/Public/Css/myfile.css" />
<ea:asset.script embed="1" identifier="myindentifier" src="EXT:myextension/Resources/Public/JavaScript/myfile.js" />
```

## Sources

- [GitHub](https://github.com/t3brightside/embedassets)
- [Packagist](https://packagist.org/packages/t3brightside/embedassets)
- [TER](https://extensions.typo3.org/extension/embedassets/)

## Development and maintenance

[Brightside OÜ – TYPO3 development and hosting specialised web agency](https://t3brightside.com/)
