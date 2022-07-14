<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class FormSignUp
{
    /**
     * @Assert\NotBlank
     * @Assert\Email()
     */
    public ?string $email = null;

    /**
     * @Assert\NotBlank
     */
    public ?string $displayName = null;

    /**
     * @Assert\NotBlank
     */
    public ?string $password = null;
}