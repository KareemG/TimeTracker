<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ResumeCommand extends Command {

	function __construct($timeSession) {
        parent::__construct();
		$this->timeSession = $timeSession;
    }

	public function configure() {
		$this->setName('resume')
			 ->setDescription('Resumes work session.')
             ->addArgument('m', InputOption::VALUE_OPTIONAL, 'Add a custom message.');
	}

	public function execute(InputInterface $input, OutputInterface $output) {
        if ($this->timeSession->folder == null) {
            exit("Folder not set. Run the 'set' command.");
        }

        if ($this->timeSession->start == null) {
            exit("Not clocked in. Run the 'clockin' command.");
        }

        $lastLog = array_slice($this->timeSession->logs, -1)[0];
        if ($lastLog->working) {
            exit("Work session already active.");
        }

		$msg = $input->getArgument('m');
        if (!$msg) {
            $msg = "Work session resumed.";
        } else {
            $msg = $msg[0];
        }

        $time = date("Y-m-d h:i:s");
        array_push($this->timeSession->logs, new Log($time, $msg, true));
        $config = fopen('config.json', 'w') or exit("Unable to write to config.");
        fwrite($config, json_encode($this->timeSession, JSON_PRETTY_PRINT));
        fclose($config);
	}
}
