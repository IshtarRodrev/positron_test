<?php

namespace App\Form;

use App\Entity\Eater;
use App\Entity\Feedback;
use App\Entity\Food;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class FeedbackAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',  TextType::class, [
                'label' => 'Name',
//                'placeholder' => 'how to address you?',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Contact e-mail',
//                'placeholder' => 'how to reach you?',
            ])
            ->add('topic', TextType::class, [
                'label' => 'Topic',
//                'placeholder' => '',
            ])
            ->add('text', TextType::class, [
                'required' => true,
                'label' => 'Your message',
//                'placeholder' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'feedback' => Feedback::class,
        ]);
    }

}
