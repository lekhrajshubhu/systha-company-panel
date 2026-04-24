<?php

namespace Systha\CompanyPanel\Application\Users\Dto;

use Illuminate\Http\Request;

final class AddressDto
{
    public function __construct(
        public string $line1,
        public ?string $line2,
        public string $city,
        public ?string $state,
        public ?string $zip,
        public ?float $lat,
        public ?float $lng,
        public ?string $country,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            line1: trim((string) $request->input('line1', '')),
            line2: self::nullableTrim($request->input('line2')),
            city: trim((string) $request->input('city', '')),
            state: self::nullableTrim($request->input('state')),
            zip: self::nullableTrim($request->input('zip')),
            lat: self::nullableFloat($request->input('lat')),
            lng: self::nullableFloat($request->input('lng')),
            country: self::nullableTrim($request->input('country')),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            line1: trim((string) ($data['line1'] ?? $data['line_1'] ?? '')),
            line2: self::nullableTrim($data['line2'] ?? $data['line_2'] ?? null),
            city: trim((string) ($data['city'] ?? '')),
            state: self::nullableTrim($data['state'] ?? null),
            zip: self::nullableTrim($data['zip'] ?? null),
            lat: self::nullableFloat($data['lat'] ?? null),
            lng: self::nullableFloat($data['lng'] ?? $data['lon'] ?? null),
            country: self::nullableTrim($data['country'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'add1' => $this->line1,
            'add2' => $this->line2,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'country' => $this->country,
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

    private static function nullableFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
