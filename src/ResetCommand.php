<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ResetCommand extends Command {


	function __construct($timeSession, $cleared) {
		parent::__construct();
		$this->timeSession = $timeSession;
        $this->cleared = $cleared;
    }

	public function configure() {
		$this->setName('reset')
			 ->setDescription('Resets work session.');
	}

	public function writeToConfig($content) {
        $config = fopen("config.json", 'w') or exit("Unable to write to config.");
        fwrite($config, $content);
        fclose($config);
    }

	public function execute(InputInterface $input, OutputInterface $output) {
		$this->writeToConfig(json_encode($this->cleared, JSON_PRETTY_PRINT));
	}
}
