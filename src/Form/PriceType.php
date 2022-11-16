<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Exception\PriceException;
use App\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PriceType extends AbstractType implements DataMapperInterface
{
    private ?DataMapperInterface $baseDataMapper;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'divisor' => 100,
            ])
            ->add('tax', PercentType::class, [
                'type' => 'integer',
                'empty_data' => '0',
            ])
            ->add('currency', CurrencyType::class);

        $this->baseDataMapper = $builder->getDataMapper();
        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Price::class);
        $resolver->setDefault('empty_data', static function (FormInterface $form) {
            try {
                return new Price(
                    (int) $form->get('amount')->getData(),
                    $form->get('tax')->getData(),
                    $form->get('currency')->getData()
                );
            } catch (PriceException $exception) {
                $form->get($exception->getProperty())
                    ->addError(new FormError($exception->getMessage()));

                return null;
            }
        });
    }

    /**
     * @param Price|null                          $viewData
     * @param \Traversable<string, FormInterface> $forms
     */
    public function mapDataToForms($viewData, \Traversable $forms): void
    {
        if (null === $this->baseDataMapper) {
            return;
        }

        $this->baseDataMapper->mapDataToForms($viewData, $forms);
    }

    /**
     * @param \Traversable<string, FormInterface> $forms
     * @param Price|null                          $viewData
     */
    public function mapFormsToData(\Traversable $forms, &$viewData): void
    {
        if (null === $viewData) {
            return;
        }

        $form = iterator_to_array($forms);

        try {
            $viewData = new Price(
                (int) $form['amount']->getData(),
                $form['tax']->getData(),
                $form['currency']->getData()
            );
        } catch (PriceException $exception) {
            $form[$exception->getProperty()]->addError(new FormError($exception->getMessage()));
        }
    }
}
