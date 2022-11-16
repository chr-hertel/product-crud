<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Exception\SkuException;
use App\Entity\Sku;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @implements DataTransformerInterface<Sku, string>
 */
final class SkuType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function transform(mixed $value): string
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof Sku) {
            throw new TransformationFailedException('Expecting Sku instance');
        }

        return $value->toString();
    }

    public function reverseTransform(mixed $value): ?Sku
    {
        if (!is_string($value)) {
            throw new TransformationFailedException('Unexpected type');
        }

        try {
            return new Sku($value);
        } catch (SkuException $exception) {
            throw new TransformationFailedException($exception->getMessage());
        }
    }
}
