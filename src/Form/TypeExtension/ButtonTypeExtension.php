<?php

declare(strict_types=1);

namespace App\Form\TypeExtension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonTypeExtension /*extends AbstractTypeExtension*/
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($builder->getCompound()) {
            $builder->add('submit', SubmitType::class);
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
