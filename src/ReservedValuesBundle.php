<?php

namespace Mikamatto\ReservedValuesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mikamatto\ReservedValuesBundle\DependencyInjection\ReservedValuesExtension;

class ReservedValuesBundle extends Bundle
{
    public function getContainerExtension(): ?\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new ReservedValuesExtension();
    }
}