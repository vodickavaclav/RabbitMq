<?php

namespace Kdyby\RabbitMq\Command;

use Kdyby\RabbitMq\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



/**
 * @author Alvaro Videla <videlalvaro@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 */
class StdInProducerCommand extends Command
{

	/**
	 * @var \Kdyby\RabbitMq\Connection
	 */
	public $connection;

	protected static $defaultName = 'rabbitmq:stdin-producer';

	public function __construct(Connection $connection)
	{
		parent::__construct();
		$this->connection = $connection;
	}

	protected function configure()
	{
		$this
			->setName('rabbitmq:stdin-producer')
			->setDescription('Creates message from given STDIN and passes it to configured producer')
			->addArgument('name', InputArgument::REQUIRED, 'Producer Name')
			->addOption('debug', 'd', InputOption::VALUE_OPTIONAL, 'Enable Debugging', false);
	}



	/**
	 * Executes the current command.
	 *
	 * @param InputInterface $input An InputInterface instance
	 * @param OutputInterface $output An OutputInterface instance
	 *
	 * @return integer 0 if everything went fine, or an error code
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		define('AMQP_DEBUG', (bool) $input->getOption('debug'));

		$producer = $this->connection->getProducer($input->getArgument('name'));

		$data = '';
		while (!feof(STDIN)) {
			$data .= fread(STDIN, 8192);
		}

		$producer->publish(serialize($data));
	}

}
