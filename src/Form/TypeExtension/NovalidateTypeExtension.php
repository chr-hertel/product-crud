<?php

declare(strict_types=1);

namespace App\Form\TypeExtension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NovalidateTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!$options['client_validation']) {
            $view->vars['attr']['novalidate'] = true;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('client_validation', false);
        $resolver->setAllowedTypes('client_validation', 'boolean');
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
