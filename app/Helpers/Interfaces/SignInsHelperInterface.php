<?php

namespace App\Helpers\Interfaces;

use App\Enum\Role;
use App\Models\Company;
use App\Models\User;

/**
 * Interface SignInsHelperInterface.
 */
interface SignInsHelperInterface
{
    /**
     *  Get an array of sign-ins that are available
     *  for the given user.
     *
     * @param User|null $user
     *
     * @return array<array{
     *     company: Company,
     *     with_password: bool,
     *     role: Role,
     *  }>
     */
    public function byUser(?User $user): array;

    /**
     * Get an array of sign-ins that are available for the phone.
     *
     * @param string $phone
     *
     * @return array<array{
     *     company: Company,
     *     with_password: bool,
     *     role: Role,
     *  }>
     */
    public function byPhone(string $phone): array;

    /**
     * Get an array of sign-ins that are available for the email.
     *
     * @param string $email
     *
     * @return array<array{
     *     company: Company,
     *     with_password: bool,
     *     role: Role,
     *  }>
     */
    public function byEmail(string $email): array;
}
