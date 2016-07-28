<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LogCommand extends Command {

	function __construct($timeSession) {
        parent::__construct();
		$this->timeSession = $timeSession;
    }

	public function configure() {
		$this->setName('log')
			 ->setDescription('Creates a log.')
			 ->addArgument('m', InputArgument::REQUIRED, 'Add msg.');
	}

	public function execute(InputInterface $input, OutputInterface $output) {
        if ($this->timeSession->folder == null) {
            exit("Folder not set. Run the 'set' command.");
        }

        if ($this->timeSession->start == null) {
            exit("Not clocked in. Run the 'clockin' command.");
        }

        $lastLog = array_slice($this->timeSession->logs, -1)[0];
        if (!$lastLog->working) {
            exit("Work session paused. Run the 'resume' command.");
        }

        $time = date("Y-m-d h:i:s");
		$msg = $input->getArgument('m');
		if ($msg) {
			array_push($this->timeSession->logs, new Log($time, $msg, true));
            $config = fopen('config.json', 'w') or exit("Unable to write to config.");
            fwrite($config, json_encode($this->timeSession, JSON_PRETTY_PRINT));
            fclose($config);
		} else {
            $output->writeln("<info>Must specify message.</info>");
        }
        $output->writeln("<info>Logged: " . $time . "</info>");
	}
}
