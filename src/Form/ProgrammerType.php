<?php


namespace App\Form;


use App\Entity\Programmer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nickname', TextType::class)
            ->add('avatarNumber', ChoiceType::class, [
                'choices' => [
                    "Girl" => 1,
                    "Boy" => 2,
                    "Cat" => 3,
                    "Boy with hat" => 4,
                    "Happy robot" => 5,
                    "Girl Purple" => 6
                ]
            ])
            ->add('tagLine', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Programmer::class
        ]);
    }

}