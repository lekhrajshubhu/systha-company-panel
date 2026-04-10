<?php

namespace Systha\CompanyPanel\Application\Users\Dto;

use Illuminate\Http\Request;

final class ClientDto
{
    public function __construct(
        public string $fname,
        public string $lname,
        public ?string $email,
        public ?string $email2,
        public ?string $phone1,
        public ?string $phone2,
        public ?string $password = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            fname: trim((string) $request->input('fname', '')),
            lname: trim((string) $request->input('lname', '')),
            email: self::nullableTrim($request->input('email')),
            email2: self::nullableTrim($request->input('email2')),
            phone1: self::nullableTrim($request->input('phone1')),
            phone2: self::nullableTrim($request->input('phone2')),
            password: self::nullableTrim($request->input('password')),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            fname: trim((string) ($data['first_name'] ?? $data['fname'] ?? '')),
            lname: trim((string) ($data['last_name'] ?? $data['lname'] ?? '')),
            email: self::nullableTrim($data['email'] ?? null),
            email2: self::nullableTrim($data['email2'] ?? null),
            phone1: self::nullableTrim($data['phone1'] ?? $data['phone'] ?? null),
            phone2: self::nullableTrim($data['phone2'] ?? null),
            password: self::nullableTrim($data['password'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'fname' => $this->fname,
            'lname' => $this->lname,
            'email' => $this->email,
            'email2' => $this->email2,
            'phone1' => $this->phone1,
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
