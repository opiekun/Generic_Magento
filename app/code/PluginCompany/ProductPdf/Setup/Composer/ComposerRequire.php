<?php
namespace PluginCompany\ProductPdf\Setup\Composer;

use Magento\Framework\Composer\MagentoComposerApplicationFactory;

/**
 * Class to run composer require command
 */
class ComposerRequire
{
    /**
     * Composer application factory
     *
     * @var MagentoComposerApplicationFactory
     */
    private $composerApplicationFactory;

    /**
     * Constructor
     *
     * @param MagentoComposerApplicationFactory $composerApplicationFactory
     */
    public function __construct(
        MagentoComposerApplicationFactory $composerApplicationFactory
    ) {
        $this->composerApplicationFactory = $composerApplicationFactory;
    }

    /**
     * Run 'composer require'
     *
     * @param array $packages
     * @throws \Exception
     *
     * @return string
     */
    public function requirePackage(array $packages)
    {
        ini_set('memory_limit', -1);
        $composerApplication = $this->composerApplicationFactory->create();

        return $composerApplication->runComposerCommand(
            [
                'command' => 'require',
                'packages' => $packages,
                '--no-interaction' => true,
            ]
        );
    }
}
