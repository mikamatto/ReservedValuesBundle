<?php 

namespace Mikamatto\ReservedValuesBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class ReservedValues extends Constraint
{
    public $message = 'The value "{{ string }}" is reserved and cannot be used.';
    public string $key; // Key for the restricted values

    public function __construct(string $key, array $options = null)
    {
        parent::__construct($options);
        $this->key = $key; // Set the key
    }

    public function validatedBy(): string
    {
        return ReservedValuesValidator::class; // Return the validator class
    }
}

