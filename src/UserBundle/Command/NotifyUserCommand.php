<?php

namespace UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Service\NotifyService;

/**
 * Class NotifyUserCommand
 *
 * @package UserBundle\Command
 */
class NotifyUserCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var NotifyService
     */
    protected $service;

    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    protected function getContainer() : ContainerInterface
    {
        if (null === $this->container) {
            $application = $this->getApplication();
            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved as the application instance.');
            }

            $this->container = $application->getKernel()->getContainer();
        }

        return $this->container;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('notify:user')
            ->setDescription('Send notifications to user');

        $this->addOption('action', 'a', InputOption::VALUE_REQUIRED, 'Specify what action do you want to perform')
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Force cron lock', false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->service = $this->getContainer()->get(NotifyService::ID);

        if ($this->service === null) {
            throw new \InvalidArgumentException('Please specify the service to be used with this command');
        }

        $method = $input->getOption('action') . 'CronAction';
        if (!method_exists(get_class($this->service), $method)) {
            throw new\InvalidArgumentException("Method {$method} doesn't exist in " . get_class($this->service));
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);

        $job = '';
        foreach ($input->getArguments() as $argument => $value) {
            if (!empty($argument)) {
                $job .= " {$value}";
            }
        }

        foreach ($input->getOptions() as $option => $value) {
            if (!empty($value)) {
                $job .= " --{$option}={$value}";
            }
        }
        $job = trim($job);

        $this->display($output, sprintf('Running %s', $job));

        if (!$this->service->run($input, $output)) {
            $this->display($output, 'Cron cannot start because it is already running.');
        }

        $end = microtime(true);
        $this->display($output, sprintf('Cron completed in %0.2f seconds.', $end - $start));
    }

    /**
     * @param OutputInterface $output
     * @param $message
     */
    protected function display(OutputInterface $output, $message)
    {
        $output->writeln('<info>[' . date('Y-m-d H:i:s') . ']</info> > ' . $message);
    }
}