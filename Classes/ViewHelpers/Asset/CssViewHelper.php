<?php

declare(strict_types=1);

namespace Brightside\Embedassets\ViewHelpers\Asset;

use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use MatthiasMullie\Minify;

final class CssViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Rendered children string is passed as CSS code,
     * there is no point in HTML encoding anything from that.
     *
     * @var bool
     */
    protected $escapeChildren = true;

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
        $this->registerArgument('href', 'string', 'Define the URL of the resource (absolute or relative).', false);
        $this->registerArgument('disabled', 'bool', 'Define whether or not the described stylesheet should be loaded and applied to the document.', false, false);
        $this->registerArgument('as', 'string', 'Define the type of content being loaded (For rel="preload" or rel="prefetch" only).', false);
        $this->registerArgument('crossorigin', 'string', 'Define how to handle crossorigin requests.', false);
        $this->registerArgument('hreflang', 'string', 'Define the language of the resource (Only to be used if \'href\' is set).', false);
        $this->registerArgument('importance', 'string', 'Define the relative fetch priority of the resource.', false);
        $this->registerArgument('integrity', 'string', 'Define base64-encoded cryptographic hash of the resource that allows browsers to verify what they fetch.', false);
        $this->registerArgument('media', 'string', 'Define which media type the resources applies to.', false);
        $this->registerArgument('referrerpolicy', 'string', 'Define which referrer is sent when fetching the resource.', false);
        $this->registerArgument('rel', 'string', 'Define the relationship of the target object to the link object.', false);
        $this->registerArgument('sizes', 'string', 'Define the icon size of the resource.', false);
        $this->registerArgument('type', 'string', 'Define the MIME type (usually \'text/css\').', false);
        $this->registerArgument('nonce', 'string', 'Define a cryptographic nonce (number used once) used to whitelist inline styles in a style-src Content-Security-Policy.', false);
        
        $this->registerArgument(
            'identifier',
            'string',
            'Use this identifier within templates to only inject your CSS once, even though it is added multiple times.',
            true
        );
        $this->registerArgument(
            'priority',
            'boolean',
            'Define whether the CSS should be included before other CSS. CSS will always be output in the <head> tag.',
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

        // The 'href' attribute is now accessed from the merged $attributes (or $this->arguments if registered)
        $file = $attributes['href'] ?? null;
        
        // Unset arguments that are specific to the ViewHelper logic, not the HTML tag
        unset(
            $attributes['identifier'],
            $attributes['priority'],
            $attributes['embed'],
            // Unset 'href' so it's not double-processed later if registered as an argument
            $attributes['href']
        );
        
        // Handle boolean attributes which shall output attr="attr" if set
        $disabledArgument = $this->arguments['disabled'] ?? false;
        if ((bool)$disabledArgument) {
            $attributes['disabled'] = 'disabled';
        } else {
            // Remove 'disabled' from attributes if it was present but false
            unset($attributes['disabled']);
        }

        $options = [
            'priority' => (bool)($this->arguments['priority'] ?? false),
        ];
        
        $embed = (bool)($this->arguments['embed'] ?? false);

        $minifier = GeneralUtility::makeInstance(Minify\CSS::class);

        if ($file !== null) {
            if ($embed) {
                $absoluteFile = GeneralUtility::getFileAbsFileName(trim($file));
                $filecontent = (string)file_get_contents($absoluteFile);
                $minifier->add($filecontent);
                $filecontent = $minifier->minify();
                $this->assetCollector->addInlineStyleSheet($identifier, $filecontent, $attributes, $options);
            } else {
                $this->assetCollector->addStyleSheet($identifier, $file, $attributes, $options);
            }
        } else {
            $content = (string)$this->renderChildren();
            if ($content !== '') {
                $minifier->add($content);
                $content = $minifier->minify();
                $this->assetCollector->addInlineStyleSheet($identifier, $content, $attributes, $options);
            }
        }
        return '';
    }
}