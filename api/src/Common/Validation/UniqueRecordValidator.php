<?php

namespace App\Common\Validation;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ReflectionObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueRecordValidator extends ConstraintValidator
{
    protected ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueRecord) {
            throw new UnexpectedTypeException($constraint, UniqueRecord::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $repo = $this->getRepo($constraint);
        $conditions = Criteria::create();
        $mappings = $constraint->mappings;
        $validationRequirements = [];

        $reflObj = new ReflectionObject($value);

        foreach ($constraint->fields as $fld) {
            try {
                $prop = $reflObj->getProperty($fld);
            } catch (\Throwable $e) {
                throw new InvalidOptionsException("Field $fld not found on object to be validated.", ['fields' => $constraint->fields]);
            }

            $validationRequirements[] = $mappings[$prop->getName()] ?? $prop->getName() . "=" . $prop->getValue($value);
            $conditions->andWhere(
                Criteria::expr()->eq(
                    $mappings[$prop->getName()] ?? $prop->getName(),
                    $prop->getValue($value)
                )
            );
        }

        if ($constraint->mode == UniqueRecordMode::UPDATE) {
            if ($constraint->existingIDFunc == null) {
                throw new MissingOptionsException("UniqueRecord: existingIDFunc must be supplied when using update mode", ['existingIDFunc']);
            }

            /** @var EntityManager $m */
            $m = $this->managerRegistry->getManagerForClass($constraint->entityClass);
            $meta = $m->getClassMetadata($constraint->entityClass);

            $conditions->andWhere(
                Criteria::expr()->neq(
                    $meta->getSingleIdentifierFieldName(),
                    $value->{$constraint->existingIDFunc}(),
                )
            );
        }

        $conflicted = ! $repo->matching($conditions)->isEmpty();

        if (! $conflicted) {
            return;
        }


        // TODO: Need to map fields to http field names if HTTPField attribute exists on props
        $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', implode(',', $validationRequirements))
                ->addViolation();
    }

    public function getRepo(UniqueRecord $constraint): EntityRepository
    {
        return $this->managerRegistry
            ->getManagerForClass($constraint->entityClass)
            ->getRepository($constraint->entityClass);
    }
}
