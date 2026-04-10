<?php

namespace Systha\CompanyPanel\Application\Vendors\Dto;

final class ContactDto
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $phone1,
        public ?string $email2 = null,
        public ?string $phone2 = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            first_name: trim((string) ($data['first_name'] ?? '')),
            last_name: trim((string) ($data['last_name'] ?? '')),
            email: trim((string) ($data['email'] ?? '')),
            phone1: trim((string) ($data['phone1'] ?? $data['phone'] ?? '')),
            email2: self::nullableTrim($data['email2'] ?? null),
            phone2: self::nullableTrim($data['phone2'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone1' => $this->phone1,
            'email2' => $this->email2,
            'phone2' => $this->phone2,
        ];
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
