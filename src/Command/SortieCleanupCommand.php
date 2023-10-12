<?php

namespace App\Command;

use App\Repository\SortieRepository;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sortie:cleanup',
    description: "Supprimer les sorties qui sont terminées depuis plus d'un mois",
)]
#[AsCronJob('*/5 * * * *')]
class SortieCleanupCommand extends Command
{
    private SortieRepository $sortieRepository;

    public function __construct(SortieRepository $sortieRepository)
    {
        parent::__construct();
        $this->sortieRepository = $sortieRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $count = 0;

        if ($input->getOption("dry-run")) {
            $io->note("Dry mode activé");
            $count = $this->sortieRepository->countSortieAfterOneMonth();
        }
        else {
            $count = $this->sortieRepository->deleteSortieAfterOneMonth();
        }

        $io->success("Suppression de $count sorties");

        return Command::SUCCESS;
    }
}
