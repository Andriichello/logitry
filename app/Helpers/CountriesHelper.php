<?php

namespace App\Helpers;

use App\Helpers\Interfaces\CountriesHelperInterface;

/**
 * Class CountriesHelper.
 */
class CountriesHelper implements CountriesHelperInterface
{
    /**
     * Returns a list of all countries with short info
     * for them:
     *  - `iso` - ISO country code
     *  - `a2` - 2-character country code
     *  - `a3` - 2-character country code
     *  - `name` - country name
     *  - `tel` - dialing code
     *  - `N` - north
     *  - `S` - south
     *  _ `W` - west
     *  _ `E` - east
     *
     * @return array<string, array{
     *      iso: int,
     *      a2: string,
     *      a3: string,
     *      name: string,
     *      tel: int | null,
     *      N: int | null,
     *      S: int | null,
     *      W: int | null,
     *      E: int | null,
     * }>
     */
    public function list(): array
    {
        $json = file_get_contents(resource_path('countries/list.json'));

        return json_decode($json, true);
    }

    /**
     * Returns a list of all country names with translations.
     *
     * @return array<string, array<string, string>>
     */
    public function translations(): array
    {
        $json = file_get_contents(resource_path('countries/translations.json'));

        return json_decode($json, true);
    }

    /**
     * Returns a list of all country names with translations.
     *
     * @param int|null $iso
     * @param string|null $a2 alpha2
     * @param string|null $a3 alpha3
     * @param string|null $name
     * @param int|null $tel
     *
     * @return array<string, array<string, string>>
     * @SuppressWarnings(PHPMD)
     */
    public function translationsFor(
        ?int $iso = null,
        ?string $a2 = null,
        ?string $a3 = null,
        ?string $name = null,
        ?int $tel = null,
    ): ?array {
        $translations = $this->translations();

        if (!empty($a2)) {
            $val = strtolower($a2);

            if (array_key_exists($val, $translations)) {
                return $translations[$val];
            }
        }

        $country = $this->findBy($iso, $a2, $a3, $name, $tel);

        if ($country && !empty($country['a2'])) {
            $val = strtolower($country['a2']);

            if (array_key_exists($val, $translations)) {
                return $translations[$val];
            }
        }

        return null;
    }

    /**
     * Find country by given parameters.
     *
     * @param int|null $iso
     * @param string|null $a2 alpha2
     * @param string|null $a3 alpha3
     * @param string|null $name
     * @param int|null $tel
     *
     * @return array|null
     * @SuppressWarnings(PHPMD)
     */
    public function findBy(
        ?int $iso = null,
        ?string $a2 = null,
        ?string $a3 = null,
        ?string $name = null,
        ?int $tel = null,
    ): ?array {
        $list = $this->list();

        if (!empty($a2)) {
            $val = strtolower($a2);

            if (array_key_exists($val, $list)) {
                return $list[$val];
            }
        }

        $search = function (array $country) use ($iso, $a2, $a3, $name, $tel): bool {
            $cIso = $country['iso'] ?? null;
            $cA2 = $country['a2'] ?? null;
            $cA3 = $country['a3'] ?? null;
            $cName = $country['name'] ?? null;
            $cTel = $country['tel'] ?? null;

            return ($iso !== null && $iso === $cIso)
                || ($a2 !== null && strcasecmp($a2, $cA2) === 0)
                || ($a3 !== null && strcasecmp($a3, $cA3) === 0)
                || ($name !== null && strcasecmp($name, $cName) === 0)
                || ($tel !== null && strcasecmp($tel, $cTel) === 0);
        };

        foreach ($list as $country) {
            if ($country === null || !$search($country)) {
                continue;
            }

            return $country;
        }

        return null;
    }
}
