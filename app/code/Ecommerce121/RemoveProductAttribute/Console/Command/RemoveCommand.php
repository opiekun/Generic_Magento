<?php

declare(strict_types=1);

namespace Ecommerce121\RemoveProductAttribute\Console\Command;

use Ecommerce121\RemoveProductAttribute\Model\Remover;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RemoveCommand
 */
class RemoveCommand extends Command
{
    /** @var Remover $remover */
    private Remover $remover;

    /**
     * Constructor.
     *
     * @param Remover $remover
     */
    public function __construct(Remover $remover)
    {
        $this->remover = $remover;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('ecommerce121:remove-product-attribute');
        $this->setDescription('Removes a product attribute.');
        $this->addArgument(
            'attribute-code',
            InputArgument::REQUIRED,
            'Attribute code'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $attributeCode = $input->getArgument('attribute-code');
        $result = $this->remover->remove($attributeCode);

        if ($result) {
            $output->writeln(sprintf('Attribute \'%s\' has been removed.', $attributeCode));

            return Cli::RETURN_SUCCESS;
        } else {
            $output->writeln(sprintf('Attribute \'%s\' cannot be removed.', $attributeCode));

            return Cli::RETURN_FAILURE;
        }
    }
}
