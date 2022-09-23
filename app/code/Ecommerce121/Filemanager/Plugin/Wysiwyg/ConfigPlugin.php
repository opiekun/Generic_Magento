<?php

declare(strict_types=1);

namespace Ecommerce121\Filemanager\Plugin\Wysiwyg;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Block\Wysiwyg\ActiveEditor;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
use Magento\Framework\DataObject;
use Ecommerce121\Filemanager\Model\Wysiwyg\ResponsiveFileManager;

class ConfigPlugin
{
    /**
     * @var ResponsiveFileManager
     */
    private $textWithBox;

    /**
     * @var ActiveEditor
     */
    private $activeEditor;

    /**
     * ConfigPlugin constructor.
     * @param ResponsiveFileManager $textWithBox
     * @param ActiveEditor $activeEditor
     */
    public function __construct(
        ResponsiveFileManager $textWithBox,
        ActiveEditor $activeEditor
    ) {
        $this->textWithBox = $textWithBox;
        $this->activeEditor = $activeEditor;
    }

    /**
     * Return WYSIWYG configuration
     *
     * @param ConfigInterface $config
     * @param DataObject $result
     *
     * @return DataObject
     * @throws NoSuchEntityException
     */
    public function afterGetConfig(ConfigInterface $config, DataObject $result) : DataObject
    {
        // Get current wysiwyg adapter's path
        $editor = $this->activeEditor->getWysiwygAdapterPath();

        // Is the current wysiwyg tinymce v4?
        if ($editor === $this->activeEditor->escapeHtml(ActiveEditor::DEFAULT_EDITOR_PATH)) {
            return $result;
        }

        $fileManagerSettings = $this->textWithBox->getPluginSettings($config);
        $result->addData($fileManagerSettings);

        return $result;
    }
}
