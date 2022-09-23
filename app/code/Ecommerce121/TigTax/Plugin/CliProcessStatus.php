<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Plugin;

use Ecommerce121\TigTax\Model\Cli\ProgressBar;
use Ecommerce121\TigTax\Model\Service\PostcodeService;

class CliProcessStatus
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @param ProgressBar $progressBar
     */
    public function __construct(
        ProgressBar $progressBar
    ) {
        $this->progressBar = $progressBar;
    }

    /**
     * @param PostcodeService $subject
     * @param string $state
     * @return string[]
     */
    // @codingStandardsIgnoreLine
    public function beforeGetPostcodes(PostcodeService $subject, string $state): array
    {
        $this->progressBar->step();

        return [$state];
    }
}
