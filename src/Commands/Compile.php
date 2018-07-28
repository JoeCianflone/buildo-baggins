<?php
namespace App\Commands;

use App\Core\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputArgument, InputOption};

class Compile extends Command {

   private $fs;

   public function __construct()
   {
      parent::__construct();

      $this->fs = new Filesystem(new Local('.'));
   }

   protected function configure()
   {
      $this
         ->setName('compile')
         ->setDescription('Will do the thing')
         ->setHelp('Does the building of things')

         ->addArgument('domain', InputArgument::REQUIRED, "URI you want to scrape")
         ->addArgument('uri', InputArgument::REQUIRED, "URI you want to scrape")
         ->addArgument('name', InputArgument::REQUIRED, "What do you want to call this new file");
   }

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      $raw = $this->getContent($input->getArgument('domain').'/'.$input->getArgument('uri'));

      $this->fs->put('latest/'.$input->getArgument('name'), str_ireplace($input->getArgument('domain').'/', '', $raw));
   }

   private function getContent($url)
   {
      $ch = curl_init();
      $timeout = 5;

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

      $data = curl_exec($ch);
      curl_close($ch);

      return $data;
   }
}
