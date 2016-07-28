<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SetFolderCommand extends Command {

	function __construct($timeSession) {
        parent::__construct();
		$this->timeSession = $timeSession;
    }

	public function configure() {
		$this->setName('set')
			 ->setDescription('Sets the folder to store work sessions.')
			 ->addArgument('folder', InputArgument::REQUIRED, 'Folder to place work sessions in.');
	}

	public function execute(InputInterface $input, OutputInterface $output) {
		$this->timeSession->folder = $input->getArgument('folder');
		$config = fopen('config.json', 'w') or exit("Unable to open config.json.");
        fwrite($config, json_encode($this->timeSession, JSON_PRETTY_PRINT));
        fclose($config);
	}
}
