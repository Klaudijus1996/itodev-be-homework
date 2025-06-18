<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterEventRequest
{
    public function __construct(
        #[Assert\NotBlank(message: "Name is required.")]
        #[Assert\Length(max: 255, maxMessage: "Name cannot be longer than {{ limit }} characters.")]
        private string $name,

        #[Assert\NotBlank(message: "Email is required.")]
        #[Assert\Email(message: "Please provide a valid email address.")]
        #[Assert\Length(max: 255, maxMessage: "Email cannot be longer than {{ limit }} characters.")]
        private string $email,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
