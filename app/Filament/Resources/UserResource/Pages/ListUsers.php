<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
        $tabs = ['All Users' => Tab::make('All Users')->badge($this->getModel()::whereHas('roles', function ($innerQuery) {
            return $innerQuery->where('name', '!=', User::ROLE_ADMIN);
        })->count())];

        $roles = Role::where('name', '!=', User::ROLE_ADMIN)->with('users')->get();

        foreach ($roles as $role) {
            $roleName = $role->name;
            $slug = str($roleName)->slug()->toString();

                $tabs[$slug] = Tab::make($roleName)
                    ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', function ($innerQuery) use ($roleName) {
                        return $innerQuery->where('name', 'LIKE', '%' . $roleName . '%');
                    }))
                    ->badge($role->users->count());

        }

        return $tabs;
    }
}
