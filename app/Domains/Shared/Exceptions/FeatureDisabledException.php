<?php

namespace App\Domains\Shared\Exceptions;

class FeatureDisabledException extends DomainException
{
    public function __construct(string $feature)
    {
        parent::__construct(
            message: "The '{$feature}' feature is not enabled.",
            errorCode: 'feature_disabled',
        );
    }
}
