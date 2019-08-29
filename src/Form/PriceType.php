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
use Traversable;

class PriceType extends AbstractType implements DataMapperInterface
{
    /**
     * @var DataMapperInterface|null
     */
    private $baseDataMapper;

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
     * @param Price|null                  $data
     * @param FormInterface[]|Traversable $forms
     */
    public function mapDataToForms($data, $forms): void
    {
        if (null === $this->baseDataMapper) {
            return;
        }

        $this->baseDataMapper->mapDataToForms($data, $forms);
    }

    /**
     * @param FormInterface[]|Traversable $forms
     * @param Price|null                  $data
     */
    public function mapFormsToData($forms, &$data): void
    {
        if (null === $data) {
            return;
        }

        $forms = iterator_to_array($forms);

        $data = new Price(
            (int) $forms['amount']->getData(),
            $forms['tax']->getData(),
            $forms['currency']->getData()
        );
    }
}
