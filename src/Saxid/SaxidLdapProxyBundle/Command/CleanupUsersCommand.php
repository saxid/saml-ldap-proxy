<?php
// src/Saxid/Command/CreateUserCommand.php
namespace Saxid\SaxidLdapProxyBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Psr\Log\LoggerInterface;
use Saxid\SaxidLdapProxyBundle\Security\User\LdapUser;

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
      /** @var $logger LoggerInterface */
      $logger = $this->getContainer()->get('logger');
      // outputs multiple lines to the console (adding "\n" at the end of each line)
      $output->writeln([
        'User Cleanup',
        '============',
        '',
      ]);

      // access the containers using getContainer()
      $sapi = $this->getContainer()->get('saxid_ldap_proxy.saxapi');
      $sldap = $this->getContainer()->get('saxid_ldap_proxy');

      try{

        // Connect to LDAP and get Users
        $sldap->connect();
        $ldapdata = $sldap->getAllLdapUser();
        $sldap->disconnect();
        // connect to API and get Users(Resources)
        $apidata = $sapi->getRessources();

        //filter expired and deletion marked Users of API
        $expiredusers = [];
        $dmusers = [];
        foreach ($apidata as $key => $value) {
          $te = strtotime($value['expiry_date']);
          $td = strtotime($value['deletion_date']);
          $tnow = strtotime(date('Y-m-d\TH:i:s.u\Z'));

          // check expired date and add them to array
          if ( $te < $tnow ) {
            $expiredusers[$key] = $value['eppn'];
          }
          //check deleted date and add them to array
          if ( $td < $tnow ) {
            $dmusers[$key] = $value['eppn'];
          }

        }

        //$output->writeln('--- expired api users ---');
        //dump($expiredusers);
        //$logger->info(print_r('Expired users ' . implode($expiredusers)));
        //$output->writeln('--- deletion marked api users ---');
        //dump($dmusers);
        //$logger->info(print_r('Deletion marked users ' . implode($dmusers)));

        // filter for eppn from api array
        unset($value);
        foreach ($apidata as $value) {
          $tmparr[] = $value['eppn'];
        }

        // filter for eduperson..-value
        unset($value);
        foreach ($ldapdata as $value) {
          $tmparr2[] = $value->getEduPersonPrincipalName();
        }

        //$output->writeln('<info>Array Diff api vs ldap - remove entries from ldap data</info>');
        $diffresult = array_diff($tmparr2, $tmparr);

        // merge diffresult (no api entry) with deletionmarked from api
        $deleteusers = array_merge($diffresult, $dmusers);

        //get dn for users to delete
        unset($value); unset($v1);
        $dntodel = [];
        foreach ($deleteusers as $key => $value) {
          foreach ($ldapdata as $k1 => $v1) {
            if($value == $v1->getEduPersonPrincipalName()) {
              $dntodel[$key] = $v1->getDn();
            }
          }
        }
        // get dn (ldap) for expired users
        unset($value); unset($v1);
        $expiredusersdn = [];
        foreach ($expiredusers as $key => $value) {
          foreach ($ldapdata as $k1 => $v1) {
            if($value == $v1->getEduPersonPrincipalName()) {
              $expiredusersdn[$key] = $v1->getDn();
            }
          }
        }
        //get UUID from API to delete
        // unset($value); unset($v1);
        // $uuidtodel = [];
        // foreach ($deleteusers as $key => $value) {
        //   foreach ($apidata as $k1 => $v1) {
        //     if($value === $v1["eppn"]) {
        //       $uuidtodel[$key] = $v1["uuid"];
        //     }
        //   }
        // }

        $output->writeln('<info>DN for users who are expired -> block them</info>');
        //dump($expiredu);

        // set new passwort and block login for expired users
        $sldap->connect();
        foreach ($expiredusersdn as $dn1){
          $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*()_-=+?";
          $password = substr(str_shuffle($chars), 0, 16);
          $sldap->setUserPassword( $dn1, $password );
          $sldap->setUserShell( $dn1, '/sbin/nologin' );
          $output->writeln(' ... pass and shell for user ' . $dn1 . ' changed in ldap ...');
        }
        $sldap->disconnect();

        $output->writeln('<info>DN / UUID for users to delete</info>');
        //finally delete users in ldap ; currently user is blocked
        $sldap->connect();
        foreach ($dntodel as $dn){
          //$sldap->deleteLDAPObject($dn);
          $sldap->setUserShell( $dn, '/sbin/nologin' );
          $output->writeln(' ... user ' . $dn . ' blocked in ldap for login...');
        }
        $sldap->disconnect();



      } catch (Exception $ex){

        $logger->error(sprintf(
            'Error in file %s at line %s: %s',
            $ex->getFile(),
            $ex->getLine(),
            $ex->getMessage()
          )
        );
        $output->write('<error>Error while cleaning up (see logfile for details) in File: ' . $ex->getFile() . '</error>\n');

      }
      // outputs a message followed by a "\n"
      $output->writeln('Finished LDAP User cleanup with SaxAPI-Data!');
    }
}
