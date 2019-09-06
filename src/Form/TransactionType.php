<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('montant')
            ->add('frais')
            ->add('total')
            ->add('commissionsup')
            ->add('commissionparte')
            ->add('commissionetat')
            /*  ->add('datedenvoie') */
            //->add('dateretrait')
            ->add('typedoperation')
            ->add('numerotransacion')
            ->add('nomExp')
            ->add('prenomExp')
            ->add('telephonExp')
            ->add('adresseExp')
            ->add('numeropieceEXp')
            ->add('typepieceExp')
            ->add('nomBen')
            ->add('prenomBen')
            ->add('telephonBen')
            ->add('adresseBen')
            ->add('numeropieceBen')
            ->add('typepieceBen')
            ->add('caissierBen', EntityType::class, ['class' => User::class])
            ->add('caissier', EntityType::class, ['class' => User::class]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
