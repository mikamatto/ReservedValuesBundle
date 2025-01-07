<?php 

namespace Mikamatto\ReservedValuesBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class ReservedValues extends Constraint
{
    public $message = 'The value "{{ string }}" is reserved and cannot be used.';
    public string $key;
    public array $bypassRoles;

    public function __construct(
        string $key,
        string|array $bypassRoles = [],
        array $options = null
    ) {
        parent::__construct($options);
        $this->key = $key;
        $this->bypassRoles = is_string($bypassRoles) ? [$bypassRoles] : $bypassRoles;
    }

    public function validatedBy(): string
    {
        return ReservedValuesValidator::class;
    }
}
