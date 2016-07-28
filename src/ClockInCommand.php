<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ClockInCommand extends Command {

	function __construct($timeSession) {
		parent::__construct();
		$this->timeSession = $timeSession;
    }

	public function configure() {
		$this->setName('clockin')
			 ->setDescription('Starts the timer for your work session.')
			 ->addArgument('m', InputOption::VALUE_OPTIONAL, 'Add a start log.');
	}

	public function writeToConfig($content) {
        $config = fopen("config.json", 'w') or exit("Unable to write to config.");
        fwrite($config, $content);
        fclose($config);
    }

	public function execute(InputInterface $input, OutputInterface $output) {
		if ($this->timeSession->folder == null) {
            exit("Folder not set. Run the 'set' command.");
        }

		if ($this->timeSession->start != null) {
            exit("Already clocked in. Run 'clockout' to exit session.");
        }

		$this->timeSession->start = date("Y-m-d h:i:s");
		$msg = $input->getArgument('m');
		if (!$msg) {
			$msg = "Work session started.";
		} else {
            $msg = $msg[0];
        }
		
		array_push($this->timeSession->logs, new Log($this->timeSession->start, $msg, true));

		$this->writeToConfig(json_encode($this->timeSession, JSON_PRETTY_PRINT));
		$output->writeln("<info>Starting: " . $this->timeSession->start . "</info>");
	}
}
