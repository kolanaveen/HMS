<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AppointmentResource;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * @return array|Tab[]
     */
    public function getTabs(): array
    {
        $tabs = [
            'All Appointments' => Tab::make('All Appointments')->badge($this->getModel()::count()),
            'Pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge($this->getModel()::where('status', 'pending')->count()),
            'Confirmed' => Tab::make('Confirmed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed'))
                ->badge($this->getModel()::where('status', 'confirmed')->count()),
            'Cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
                ->badge($this->getModel()::where('status', 'cancelled')->count()),
        ];

        return $tabs;
    }
}
