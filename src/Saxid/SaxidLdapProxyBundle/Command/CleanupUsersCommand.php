<?php
// src/Saxid/Command/CreateUserCommand.php
namespace Saxid\SaxidLdapProxyBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CleanupUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
      $this
        // the name of the command (the part after "bin/console")
        ->setName('saxid_ldap_proxy:cleanup-users')

        // the short description shown while running "php bin/console list"
        ->setDescription('Cleaning up SaxID-Users.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp("This command allows you to cleanup expired users from LDAP with the help of SaxID-API. For sheduled usage please setup an cronjob.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      // outputs multiple lines to the console (adding "\n" at the end of each line)
      $output->writeln([
        '<info>User Cleanup</info>',
        '============',
        '',
      ]);

      // access the containers using getContainer()
      $sapi = $this->getContainer()->get('saxid_ldap_proxy.saxapi');
      $sldap = $this->getContainer()->get('saxid_ldap_proxy');

      try{

        // Connect to LDAP and get all Users
        $sldap->connect();
        $ldapdata = $sldap->getUserData('(uid=*)', array("eduPersonPrincipalName"));
        $sldap->disconnect();

        //get all Users from Api
        $apidata = $sapi->getRessources();

        foreach ($apidata as $key => $value) {
          $tmparr[$key] = $value['eppn'];
        }

        foreach ($ldapdata as $key => $value) {
          $tmparr2[$key] = $value[0];
        }

        $result = array_diff($tmparr2, $tmparr);
        dump($result);


        //todo delete data from resultset, check expiry date before
        // FOREACH in $result do ...
        //$sldap->deleteLDAPObject("cn=" . getCommonName() . ",o=" . getAcademyDomain() . ",dc=sax-id,dc=de");

      } catch (Exception $ex){

        $output->write('<error>Error while processing data: ',  $ex->getMessage(), "</error>\n");

      }
      // outputs a message followed by a "\n"
      $output->writeln('<info>Finished!</info>');
    }
}
