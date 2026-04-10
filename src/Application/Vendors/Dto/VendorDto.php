<?php

namespace Systha\CompanyPanel\Application\Vendors\Dto;

final class VendorDto
{
    public function __construct(
        public string $name,
        public ?string $type,
        public ?string $email,
        public ?string $phone,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) ($data['name'] ?? '')),
            type: trim((string) ($data['type'] ?? '')),
            email: self::nullableTrim($data['email'] ?? null),
            phone: self::nullableTrim($data['phone'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'email' => $this->email,
            'phone' => $this->phone,
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
