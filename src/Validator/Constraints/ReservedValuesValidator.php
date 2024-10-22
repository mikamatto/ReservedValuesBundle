<?php

namespace Mikamatto\ReservedValuesBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReservedValuesValidator extends ConstraintValidator
{
    public function __construct(private ContainerInterface $container)
    {
    }
    
    public function validate($value, Constraint $constraint)
    {
        // Fetch the restricted values based on the provided key
        $key = $constraint->key;  // Get the key from the constraint
        $exact = $this->container->getParameter("reserved_values.$key.exact");
        $patterns = $this->container->getParameter("reserved_values.$key.patterns");

        // Convert value to lowercase for case-insensitive matching
        $valueLower = strtolower($value);

        // Validate against exact matches
        if (in_array($valueLower, array_map('strtolower', $exact))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
            return;
        }

        // Validate against patterns
        foreach ($patterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $valueLower)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
                return;
            }
        }
    }
}