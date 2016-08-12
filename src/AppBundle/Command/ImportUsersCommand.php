<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;

class ImportUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:import-users')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates new users from CSV file.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to create users...")

            // get the CSV file name
            ->addArgument('CSVfilename', InputArgument::REQUIRED, 'The file name with users list in CSV format located in the `data` directory.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $CSVfilename = $input->getArgument('CSVfilename');

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'User Importer',
            '============',
            '',
        ]);

        $output->writeln("File name: $CSVfilename");

        // Manage users
        $users = $this->existingUsers();

        // $output->writeln(var_dump($users));

        // Get data from CSV file
        $CSVdata = $this->readCSVFile($CSVfilename);

        $this->updateOrCreateUsers($users, $CSVdata, $output);
    }

    private function existingUsers()
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();
        return $users;
    }

    private function readCSVFile($filename)
    {
        $CSVfilepath = dirname(__DIR__).'/../../../'.$filename;

        $result = array();

        if (($handle = fopen($CSVfilepath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $result[] = array(
                    "firstName" => $data[0],
                    "lastName" => $data[1],
                    "email" => $data[2],
                );
            }
            fclose($handle);
        }

        return $result;
    }

    private function updateOrCreateUsers($users, $CSVdata, $output)
    {
        $newUserObjects = array(); // keep track of new user objects
        $oldUserObjects = array();

        foreach ($CSVdata as $newUser) {
            $newUserEmail = trim($newUser['email']);

            // Filter existing from new users
            $foundUser = NULL;
            foreach ($users as $user) {
                // Check for user by email, as it should be unique
                if (trim($user->getUsername()) == $newUserEmail) {
                    $foundUser = $user;
                }
            }

            if (isset($foundUser)) {
                $oldUserObjects[] = $this->updateUserObj($foundUser, $newUser);
            } else {
                $newUserObjects[] = $this->newUserObj($newUser);
            }
        }

        $output->writeln("New users: ".count($newUserObjects));
        $output->writeln("Updated users: ".count($oldUserObjects));

        // Persist updated and new users
        $this->persistsUsers($newUserObjects);
        $this->persistsUsers($oldUserObjects);
    }

    private function newUserObj($userData)
    {
        $user = new User();

        $plainPassword = trim($userData['firstName']);
        $password = $this->securityPasswordEncoder()
                         ->encodePassword($user, $plainPassword);

        // Set user attributes
        $user->setPassword($password);
        $user->setFirstName(trim($userData['firstName']));
        $user->setLastName(trim($userData['lastName']));
        $user->setUsername(trim($userData['email']));
        return $user;
    }

    private function securityPasswordEncoder()
    {
        return $this->getApplication()
                    ->getKernel()
                    ->getContainer()
                    ->get('security.password_encoder');
    }

    private function updateUserObj($user, $newUser)
    {
        $user->setUsername(trim($newUser['email']));
        $user->setFirstName(trim($newUser['firstName']));
        $user->setLastName(trim($newUser['lastName']));
        return $user;
    }

    private function persistsUsers($userObjects)
    {
        // Get doctrine entity manager
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        // Persist
        foreach ($userObjects as $user) {
            $em->persist($user);
        }
        $em->flush();
    }
}
