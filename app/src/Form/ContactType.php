<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'label' => 'Votre prénom',
            ])
            ->add('email',EmailType::class, [
                'label' => 'Votre adresse mail',
            ])
            ->add('subject',TextType::class, [
                'label' => 'Votre message',
                'constraints' => [
                    new NotBlank(message:"Veuillez saisir votre message"),
                    new Length(min: 50, minMessage: 'Votre message doit faire au minimum 50 caractères')
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
