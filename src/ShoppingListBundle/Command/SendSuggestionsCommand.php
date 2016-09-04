<?php
namespace ShoppingListBundle\Command;

use ShoppingListBundle\Service\NotificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SendSuggestionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopping:sendSuggestions')
            ->setDescription('Send suggestions to user')
            ->setHelp("This command allows you to send notifications based on buy frequency");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var NotificationService $notificationService */
        $notificationService = $this->getContainer()->get('hack2016.notification.service');
        $notificationService->sendNotifications($output);
        $output->write('DONE!');
    }
}