<?php

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class UniqueProvinceCollectionValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /** @var Collection<array-key, ProvinceInterface> $value */
        Assert::allIsInstanceOf($value, ProvinceInterface::class);
        /** @var UniqueProvinceCollection $constraint */
        Assert::isInstanceOf($constraint, UniqueProvinceCollection::class);

        if ($value->isEmpty()) {
            return;
        }

        $provincesWithAnyRequiredData = $value->filter(
            fn(ProvinceInterface $province): bool => null !== $province->getCode() || null !== $province->getName()
        );

        $codes = [];
        $names = [];
        foreach ($provincesWithAnyRequiredData as $province) {
            $code = $province->getCode();
            $name = $province->getName();

            if (isset($code) && in_array($code, $codes) || isset($name) && in_array($name, $names)) {
                $this->context->addViolation($constraint->message);

                return;
            }

            $codes[] = $code;
            $names[] = $name;
        }
    }
}
