<?php

namespace Systha\CompanyPanel\Application\Users\Services;

use Illuminate\Database\Eloquent\Model;
use Systha\CompanyPanel\Application\Users\Dto\AddressDto;
use Systha\Core\Models\AddressModel;

class AddressService
{
    public function createFor(Model $model, AddressDto $dto): AddressModel
    {
        $model->addresses()->update(['is_default' => false]);

        $data = $dto->toArray();
        $data['is_default'] = true;

        return $model->addresses()->create($data);
    }

    public function update(AddressModel $address, AddressDto $dto): AddressModel
    {
        $data = $dto->toArray();
        $address->update($data);
        return $address->refresh();
    }

    public function getDefault(Model $model): ?AddressModel
    {
        return $model->addresses()
            ->where('is_default', true)
            ->first();
    }

    public function createOrUpdateDefault(Model $model, AddressDto $dto): AddressModel
    {
        $address = $this->getDefault($model);

        if ($address) {
            return $this->update($address, $dto);
        }

        return $this->createFor($model, $dto);
    }

    public function setAsDefault(AddressModel $address): AddressModel
    {
        $model = $address->addressable;
        if (!$model) {
            return $address->refresh();
        }

        $model->addresses()->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        return $address->refresh();
    }
}
