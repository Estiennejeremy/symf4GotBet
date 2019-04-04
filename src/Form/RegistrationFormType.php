<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType; 
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre nom doit faire au minimum {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 15,
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prénom.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre prénom doit faire au minimum {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                    ]),
                ],
            ])
            ->add('mail', TextType::class, [
                'label' => 'Mail :',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'constraints' => [
                    new Notblank([
                        'message' => 'Veuillez entrer un email.',
                    ]),
                    new Email([
                        'message' => 'Ce mail sera utilisé pour vous contacter en cas de victoire.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe :',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au minimum {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('giveInfo', CheckboxType::class, [
                'mapped' => false,
                'label'    => 'En soumettant ce formulaire, j\'accepte que les informations saisies soient utilisées pour me contacter dans le cadre du concours GotBet.',
                'required' => true,
            ])
            ->add('captchaCode', CaptchaType::class, array('captchaConfig' => 'ExampleCaptcha')); 
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
