<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DocumentResource;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['document'] = $data['filepath'];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['document'] instanceof TemporaryUploadedFile) {
            $file = $data['document'];
            $filepath =  $file->store('documents', 'public');

            $data['filename'] = $file->getClientOriginalName();
            $data['filesize'] = $file->getSize();
            $data['filepath'] = $filepath;
        }
        unset($data['document']);

        return $data;
    }
}
