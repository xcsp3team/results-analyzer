<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('track')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('defaulttime')
                    ->required()
                    ->numeric(),
                Toggle::make('public')
                    ->required(),
                Textarea::make('rank')
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->required(),
            ]);
    }
}
