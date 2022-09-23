<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model\Cli;

use Ecommerce121\TigTax\Model\RegionProvider;
use Symfony\Component\Console\Helper\ProgressBar as ConsoleProgressBar;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressBar
{
    /**
     * @var RegionProvider
     */
    private $regionProvider;

    /**
     * @var ProgressBarFactory
     */
    private $progressBarFactory;

    /**
     * @var ConsoleProgressBar|null
     */
    private $progressBar;

    /**
     * @param RegionProvider $regionProvider
     * @param ProgressBarFactory $progressBarFactory
     */
    public function __construct(
        RegionProvider $regionProvider,
        ProgressBarFactory $progressBarFactory
    ) {
        $this->regionProvider = $regionProvider;
        $this->progressBarFactory = $progressBarFactory;
    }

    /**
     * @param OutputInterface $output
     */
    public function start(OutputInterface $output)
    {
        $this->progressBar = $this->progressBarFactory->create(
            [
                'output' => $output,
                'max' => count($this->regionProvider->getRegions()),
            ]
        );

        $this->progressBar->setFormat(
            '%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%'
        );

        $this->progressBar->start();
    }

    /**
     * @return void
     */
    public function step()
    {
        if ($this->progressBar instanceof ConsoleProgressBar) {
            $this->progressBar->advance();
        }
    }

    /**
     * @return void
     */
    public function finish()
    {
        if ($this->progressBar instanceof ConsoleProgressBar) {
            $this->progressBar->finish();
        }
    }
}
