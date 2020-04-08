<?php

namespace Kdyby\RabbitMq\Command;

use Kdyby\RabbitMq\AmqpMember;
use Kdyby\RabbitMq\Connection;
use Kdyby\RabbitMq\DI\RabbitMqExtension;
use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



/**
 * @author Alvaro Videla <videlalvaro@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 */
class SetupFabricCommand extends Command
{

	/**
	 * @var \Nette\DI\Container
	 */
	public $container;

	protected static $defaultName = 'rabbitmq:setup-fabric';

	public function __construct(Container $container)
	{
		parent::__construct();
		$this->container = $container;
	}

	protected function configure()
	{
		$this
			->setName('rabbitmq:setup-fabric')
			->setDescription('Sets up the Rabbit MQ fabric')
			->addOption('debug', 'd', InputOption::VALUE_NONE, 'Enable Debugging');
	}



	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if (defined('AMQP_DEBUG') === false) {
			define('AMQP_DEBUG', (bool) $input->getOption('debug'));
		}

		$output->writeln('Setting up the Rabbit MQ fabric');

		foreach ([
			RabbitMqExtension::TAG_PRODUCER,
			RabbitMqExtension::TAG_CONSUMER,
			RabbitMqExtension::TAG_RPC_CLIENT,
			RabbitMqExtension::TAG_RPC_SERVER
		] as $tag) {
			foreach ($this->container->findByTag($tag) as $serviceId => $meta) {
				/** @var AmqpMember $service */
				$service = $this->container->getService($serviceId);
				$service->setupFabric();
			}
		}
		return 0;
	}

}
