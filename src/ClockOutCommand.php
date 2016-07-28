<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ClockOutCommand extends Command {

	function __construct($timeSession, $cleared) {
        parent::__construct();
		$this->timeSession = $timeSession;
        $this->cleared = $cleared;
    }

	public function configure() {
		$this->setName('clockout')
			 ->setDescription('Stops the timer for your work session.')
			 ->addArgument('m', InputOption::VALUE_OPTIONAL, 'Add an end log.');
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

        if ($this->timeSession->start == null) {
            exit("Not clocked in. Run the 'clockin' command.");
        }

        $lastLog = array_pop($this->timeSession->logs);
        if (!$lastLog->working) {
            $end = $lastLog->time;
        } else {
            array_push($this->timeSession->logs, $lastLog);
            $end = date("Y-m-d h:i:s");
        }

		$msg = $input->getArgument('m');
		if (!$msg) {
			$msg = "Work session ended.";
		} else {
            $msg = $msg[0];
        }
        
        $this->timeSession->end = $end;
        array_push($this->timeSession->logs, new Log($end, $msg, true));

        $diff = abs(strtotime($end) - strtotime($this->timeSession->start));

        $check = false;
        $time;
        foreach ($this->timeSession->logs as $log) {
            if ($check) {
                $diff -= abs(strtotime($log->time) - strtotime($time));
                $check = false;
            }
            if (!$log->working) {
                $check = true;
                $time = $log->time;
            }
        }

        $output->writeln("<info>Finished: " . $end . "</info>");

        $start = str_replace(':', '-', $this->timeSession->start);
        $end = str_replace(':', '-', $end);

        $this->timeSession->total = $diff;
        $config = fopen($this->timeSession->folder . "/" . $start . '~' . $end . '.json', 'w') or exit("Unable to write to folder.");
        fwrite($config, json_encode($this->timeSession, JSON_PRETTY_PRINT));
        fclose($config);

        $this->cleared->folder = $this->timeSession->folder;

        $this->writeToConfig(json_encode($this->cleared, JSON_PRETTY_PRINT));
		
	}
}
