<?php

namespace Saxid\SaxidLdapProxyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, array(
              'type' => PasswordType::class,
              'invalid_message' => 'Die Passwörter müssen übereinstimmen.',
              'options' => array('attr' => array('class' => 'form-control')),
              'required' => true,
              'first_options'  => array('label' => 'Passwort'),
              'second_options' => array('label' => 'Passwort wiederholen')
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Password ändern',
                'attr' => array(
                  'class' => 'btn btn-primary'
                )
            ))
            ->add('generate', SubmitType::class, array(
                'validation_groups' => false,
                'label' => 'Password erzeugen',
                'attr' => array(
                  'class' => 'btn btn-default'
                )
            ))
        ;
    }
}
