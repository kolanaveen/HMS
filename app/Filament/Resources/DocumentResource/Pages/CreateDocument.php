<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $file = $data['document'];

        if ($file) {
            $filepath =  $file->store('documents', 'public');

            $data['filename'] = $file->getClientOriginalName();
            $data['filesize'] = $file->getSize();
            $data['filepath'] = $filepath;
        }

        unset($data['document']);

        return $data;
    }
}
