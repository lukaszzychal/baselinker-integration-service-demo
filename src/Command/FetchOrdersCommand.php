<?php

declare(strict_types=1);

namespace App\Command;

use App\DTO\FetchOrdersRequest;
use App\UseCase\FetchOrdersUseCaseInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fetch-orders')]
class FetchOrdersCommand extends Command
{
    public function __construct(
        private readonly FetchOrdersUseCaseInterface $useCase,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Fetches orders from Baselinker')
            ->addOption('marketplace', 'm', InputOption::VALUE_REQUIRED, 'Marketplace name', 'all')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Date from (Y-m-d)', (new \DateTimeImmutable('-1 day'))->format('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');

        try {
            $date = new \DateTimeImmutable($from);
        } catch (\Exception $e) {
            $output->writeln('<error>Invalid date format. Use Y-m-d.</error>');

            return Command::FAILURE;
        }

        $marketplace = $input->getOption('marketplace');

        $request = new FetchOrdersRequest(
            date_from: $date->getTimestamp(),
            filter_order_source: 'all' === $marketplace ? null : $marketplace
        );

        $this->useCase->execute($request);

        $output->writeln('<info>Orders fetch command dispatched successfully!</info>');

        return Command::SUCCESS;
    }
}
