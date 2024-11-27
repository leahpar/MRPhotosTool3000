<?php

namespace App\Command;

use App\Entity\Stat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:stats',
    description: 'calcul stats',
)]
class StatsCommand extends Command
{
    public function __construct(
        private readonly string $igToken,
        private readonly string $igAccountId,
        private readonly string $hcToken,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $url = "https://graph.facebook.com/v16.0/" . $this->igAccountId;
            $params = [
                "fields" => "id,name,followers_count",
                "access_token" => $this->igToken
            ];
            $url .= "?" . http_build_query($params);
            // TODO: gestion erreur HTTP != 200
            $json = file_get_contents($url);
            $data = json_decode($json, null, 512, JSON_THROW_ON_ERROR);

            /** @var Stat $stat */
            $stat = $this->em->getRepository(Stat::class)->findOneBy([
                'date' => new \DateTime()
            ]) ?? new Stat();

            $stat->name = "followers_count";
            $stat->value = $data->followers_count;
            $this->em->persist($stat);
            $this->em->flush();

            if ($this->hcToken) {
                // = requete HTTP GET
                file_get_contents('https://hc-ping.com/'.$this->hcToken);
            }
            $io->success($data->followers_count);
            return Command::SUCCESS;
        }
        catch (\Exception $e) {
            $io->error($e->getMessage());
            if ($this->hcToken) {
                // = requete HTTP GET
                file_get_contents('https://hc-ping.com/'.$this->hcToken.'/fail');
            }
            return Command::FAILURE;
        }
    }

}
