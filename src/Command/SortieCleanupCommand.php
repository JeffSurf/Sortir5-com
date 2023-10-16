<?php

namespace App\Command;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use App\Service\ParticipantService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sortie:cleanup',
    description: "Supprimer les sorties qui sont terminées depuis plus d'un mois",
)]
class SortieCleanupCommand extends Command
{
    private SortieRepository $sortieRepository;
    private ParticipantService $participantService;

    public function __construct(SortieRepository $sortieRepository, ParticipantService $participantService)
    {
        parent::__construct();
        $this->sortieRepository = $sortieRepository;
        $this->participantService = $participantService;
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

        if ($input->getOption("dry-run")) {
            $io->note("Dry mode activé");
            $count = $this->sortieRepository->countSortieAfterOneMonth();
        }
        else {

            $sorties = $this->sortieRepository->getSortieAfterOneMonth()->getQuery()->execute();

            /** @var Sortie $sortie */
            foreach ($sorties as $sortie)
            {
                /** @var Participant $participant */
                foreach ($sortie->getParticipants() as &$participant)
                {
                    $this->participantService->archive($participant, $sortie);
                }
            }

            $count = $this->sortieRepository->deleteSortieAfterOneMonth();
        }

        $io->success("Suppression de $count sorties");

        return Command::SUCCESS;
    }
}
