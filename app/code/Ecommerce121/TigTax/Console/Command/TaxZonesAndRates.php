<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Console\Command;

use Ecommerce121\TigTax\Model\Cli\ProgressBar;
use Ecommerce121\TigTax\Model\StoreConfig;
use Ecommerce121\TigTax\Model\TigTaxProcessor;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaxZonesAndRates extends Command
{
    /**
     * @var StoreConfig
     */
    private $storeConfig;

    /**
     * @var TigTaxProcessor
     */
    private $tigTaxProcessor;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @param StoreConfig $storeConfig
     * @param TigTaxProcessor $tigTaxProcessor
     * @param ProgressBar $progressBar
     * @param string|null $name
     */
    public function __construct(
        StoreConfig $storeConfig,
        TigTaxProcessor $tigTaxProcessor,
        ProgressBar $progressBar,
        string $name = null
    ) {
        parent::__construct($name);

        $this->storeConfig = $storeConfig;
        $this->tigTaxProcessor = $tigTaxProcessor;
        $this->progressBar = $progressBar;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('tigtax:get:zones-and-rates');
        $this->setDescription('TigTax Integrations. Loads tax zones and rates');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->storeConfig->isEnabled()) {
            $output->writeln(
                '<comment>TigTax Integration is disabled. '
                . 'Enable it here: Configuration > Sales > Tax > TigTax Settings > Enable.</comment>'
            );
            return Cli::RETURN_FAILURE;
        }

        try {
            $this->progressBar->start($output);
            if ($this->tigTaxProcessor->execute()) {
                $output->write(PHP_EOL);
                $output->writeln('<info>Success. TigTax Integration.</info>');
                return Cli::RETURN_SUCCESS;
            }
            $this->progressBar->finish();

            $output->write(PHP_EOL);
            $output->writeln('<comment>TigTax Integration is going. Skipped.</comment>');
            return Cli::RETURN_FAILURE;
        } catch (LocalizedException $e) {
            $output->write(PHP_EOL);
            $output->writeln('<error>An error encountered: ' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }
    }
}
