<?php

namespace Systha\CompanyPanel\Application\Company\Dto;

final class CompanyDto
{
    public function __construct(
        public readonly bool $hasName,
        public readonly ?string $name,
        public readonly bool $hasEmail,
        public readonly ?string $email,
        public readonly bool $hasEmailAlt,
        public readonly ?string $emailAlt,
        public readonly bool $hasPhone,
        public readonly ?string $phone,
        public readonly bool $hasPhoneAlt,
        public readonly ?string $phoneAlt,
        public readonly bool $hasMeta,
        public readonly ?array $meta,
        public readonly bool $hasUrl,
        public readonly ?string $url,
        public readonly bool $hasRegNo,
        public readonly ?string $regNo,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        [$hasName, $name] = self::resolveWithFallback($payload, 'name', 'company_name');
        [$hasEmail, $email] = self::resolveWithFallback($payload, 'email', 'company_email');
        [$hasPhone, $phone] = self::resolveWithFallback($payload, 'phone', 'company_phone');

        $hasMeta = array_key_exists('meta', $payload);
        $meta = $hasMeta && is_array($payload['meta']) ? $payload['meta'] : null;

        return new self(
            hasName: $hasName,
            name: self::nullableTrim($name),
            hasEmail: $hasEmail,
            email: self::nullableTrim($email),
            hasEmailAlt: array_key_exists('email_alt', $payload),
            emailAlt: self::nullableTrim($payload['email_alt'] ?? null),
            hasPhone: $hasPhone,
            phone: self::nullableTrim($phone),
            hasPhoneAlt: array_key_exists('phone_alt', $payload),
            phoneAlt: self::nullableTrim($payload['phone_alt'] ?? null),
            hasMeta: $hasMeta,
            meta: $meta,
            hasUrl: array_key_exists('url', $payload),
            url: self::nullableTrim($payload['url'] ?? null),
            hasRegNo: array_key_exists('reg_no', $payload),
            regNo: self::nullableTrim($payload['reg_no'] ?? null),
        );
    }

    /**
     * @return array{bool,mixed}
     */
    private static function resolveWithFallback(array $payload, string $primary, string $fallback): array
    {
        if (array_key_exists($primary, $payload)) {
            return [true, $payload[$primary]];
        }

        if (array_key_exists($fallback, $payload)) {
            return [true, $payload[$fallback]];
        }

        return [false, null];
    }

    private static function nullableTrim(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
