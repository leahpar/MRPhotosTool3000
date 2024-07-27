<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ResetPwdTrait
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetPasswordToken = null;

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }


}
