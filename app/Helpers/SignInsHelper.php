<?php

namespace App\Helpers;

use App\Enum\Role;
use App\Helpers\Interfaces\SignInsHelperInterface;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;

/**
 * Class SignInsHelper.
 */
class SignInsHelper implements SignInsHelperInterface
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
    public function byUser(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $companyRoles = $user->companyRoles();

        $signIns = [];

        /** @var Company $company */
        foreach ($user->companies as $company) {
            $signIn = Arr::first(
                $signIns,
                fn ($p) => data_get($p, 'company.id') === $company->id
            );

            if (empty($signIn)) {
                $signIns[] = [
                    'company' => $company,
                    'with_password' => true,
                    'role' => data_get($companyRoles, $company->id),
                ];

                foreach ($company->subCompanies as $subCompany) {
                    $signIn = Arr::first(
                        $signIns,
                        fn ($p) => data_get($p, 'company.id') === $subCompany->id
                    );

                    if (empty($signIn)) {
                        $signIns[] = [
                            'company' => $subCompany,
                            'with_password' => true,
                            'role' => data_get($companyRoles, $subCompany->id),
                        ];
                    }
                }
            }
        }

        return $signIns;
    }

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
    public function byPhone(string $phone): array
    {
        /** @var User $user */
        $user = User::query()
            ->where('phone', $phone)
            ->first();

        return $this->filterOutUnavailableAndSort($this->byUser($user));
    }

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
    public function byEmail(string $email): array
    {
        /** @var User $user */
        $user = User::query()
            ->where('email', $email)
            ->first();

        return $this->filterOutUnavailableAndSort($this->byUser($user));
    }

    /**
     * Filters out sign-ins that are not available
     * (`with_password === false`).
     *
     * @param array $signIns
     *
     * @return array
     */
    protected function filterOutUnavailable(array $signIns): array
    {
        return array_filter(
            $signIns,
            fn (array $signIn) => $signIn['with_password']
                || !empty($signIn['google_url'])
                || !empty($signIn['microsoft_url'])
        );
    }

    /**
     * Filters out sign-ins that are not available
     * (`with_password === false`)
     * and then sorts them into company groups (company and then
     * it's sub-companies, next company...).
     *
     * @param array $signIns
     *
     * @return array
     */
    protected function filterOutUnavailableAndSort(array $signIns): array
    {
        $filtered = $this->filterOutUnavailable($signIns);

        return $this->sort($filtered);
    }

    /**
     * Sorts sign-ins into company groups (first company
     * and then it's sub-companies, next company...).
     *
     * @param array $signIns
     *
     * @return array
     */
    protected function sort(array $signIns): array
    {
        usort(
            $signIns,
            function (array $one, array $two) {
                /** @var Company $companyOne */
                $companyOne = data_get($one, 'company');
                /** @var Company $companyTwo */
                $companyTwo = data_get($two, 'company');

                $oneIsSub = isset($companyOne->parent_id);
                $twoIsSub = isset($companyTwo->parent_id);

                if ($oneIsSub && $twoIsSub) {
                    return $companyOne->parent_id <=> $companyTwo->parent_id;
                }

                /** @phpstan-ignore-next-line */
                if ($oneIsSub && !$twoIsSub) {
                    if ($companyOne->parent_id === $companyTwo->id) {
                        return 1;
                    }

                    return $companyOne->parent_id <=> $companyTwo->id;
                }

                if (!$oneIsSub && $twoIsSub) {
                    if ($companyOne->id === $companyTwo->parent_id) {
                        return -1;
                    }

                    return $companyOne->id <=> $companyTwo->parent_id;
                }

                return $companyOne->id <=> $companyTwo->id;
            }
        );

        return $signIns;
    }
}
