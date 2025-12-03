<?php

declare(strict_types=1);

namespace Brightside\Embedassets\ViewHelpers\Asset;

use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use MatthiasMullie\Minify;

final class ScriptViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * This VH does not produce direct output, thus does not need to be wrapped in an escaping node
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Rendered children string is passed as JavaScript code,
     * there is no point in HTML encoding anything from that.
     *
     * @var bool
     */
    protected $escapeChildren = false;

    protected AssetCollector $assetCollector;

    public function injectAssetCollector(AssetCollector $assetCollector): void
    {
        $this->assetCollector = $assetCollector;
    }

    public function initialize(): void
    {
        $this->setTagBuilder(
            new class () extends TagBuilder {
                public function addAttribute($attributeName, $attributeValue, $escapeSpecialCharacters = false): void
                {
                    parent::addAttribute($attributeName, $attributeValue, false);
                }
            }
        );
        parent::initialize();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('async', 'bool', 'Define that the script will be fetched in parallel to parsing and evaluation.', false, false);
        $this->registerArgument('defer', 'bool', 'Define that the script is meant to be executed after the document has been parsed.', false, false);
        $this->registerArgument('nomodule', 'bool', 'Define that the script should not be executed in browsers that support ES2015 modules.', false, false);
        $this->registerArgument('src', 'string', 'Define the URI of the external resource.', false);
        $this->registerArgument('crossorigin', 'string', 'Define how to handle crossorigin requests.', false);
        $this->registerArgument('integrity', 'string', 'Define base64-encoded cryptographic hash of the resource that allows browsers to verify what they fetch.', false);
        $this->registerArgument('nonce', 'string', 'Define a cryptographic nonce (number used once) used to whitelist inline styles in a style-src Content-Security-Policy.', false);
        $this->registerArgument('referrerpolicy', 'string', 'Define which referrer is sent when fetching the resource.', false);
        $this->registerArgument('type', 'string', 'Define the MIME type (usually \'text/javascript\').', false);
        
        $this->registerArgument(
            'identifier',
            'string',
            'Use this identifier within templates to only inject your JS once, even though it is added multiple times.',
            true
        );
        $this->registerArgument(
            'priority',
            'boolean',
            'Define whether the JavaScript should be put in the <head> tag above-the-fold or somewhere in the body part.',
            false,
            false
        );
        $this->registerArgument(
            'embed',
            'boolean',
            'Define whether or not the described stylesheet should be embedded into HTML output.',
            false,
            false
        );
    }

    public function render(): string
    {
        $identifier = (string)$this->arguments['identifier'];
        
        // Collect all registered arguments and additional attributes
        $attributes = array_merge(
            $this->arguments,
            $this->additionalArguments
        );

        // The 'src' attribute is now accessed from the arguments
        $src = $attributes['src'] ?? null;
        
        // Unset arguments that are specific to the ViewHelper logic, not the HTML tag
        unset(
            $attributes['identifier'],
            $attributes['priority'],
            $attributes['embed'],
            // Unset 'src' so it's not double-processed later if registered as an argument
            $attributes['src']
        );

        // boolean attributes shall output attr="attr" if set.
        // We must check the boolean value from $this->arguments
        foreach (['async', 'defer', 'nomodule'] as $attrName) {
            $attrValue = $this->arguments[$attrName] ?? false;
            if ((bool)$attrValue) {
                $attributes[$attrName] = $attrName;
            } else {
                // Ensure it is removed if it was present in additionalArguments but explicitly false
                unset($attributes[$attrName]);
            }
        }

        $options = [
            'priority' => (bool)($this->arguments['priority'] ?? false),
        ];

        $embed = (bool)($this->arguments['embed'] ?? false);

        $minifier = GeneralUtility::makeInstance(Minify\JS::class);

        if ($src !== null) {
            if ($embed) {
                $absoluteFile = GeneralUtility::getFileAbsFileName(trim($src));
                $content = (string)file_get_contents($absoluteFile);
                $minifier->add($content);
                $content = $minifier->minify();
                $this->assetCollector->addInlineJavaScript($identifier, $content, $attributes, $options);
            } else {
                $this->assetCollector->addJavaScript($identifier, $src, $attributes, $options);
            }
        } else {
            $content = (string)$this->renderChildren();
            if ($content !== '') {
                $minifier->add($content);
                $content = $minifier->minify();
                $this->assetCollector->addInlineJavaScript($identifier, $content, $attributes, $options);
            }
        }
        return '';
    }
}