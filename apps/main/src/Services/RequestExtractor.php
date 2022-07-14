<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestExtractor
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function getAcceptLanguage(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request?->headers->get('accept-language');

    }
}