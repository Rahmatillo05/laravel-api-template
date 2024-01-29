<?php

namespace App\Providers\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Contracts\Support\Htmlable;

class Login extends \Filament\Pages\Auth\Login
{
    public static ?string $title = 'Sign in to your account';

//    public static string $view = 'app.pages.login';
    protected ?string $heading = 'Akkauntga kirish';

    protected ?string $subheading = 'Akkauntingizga kirish uchun telefon raqamingizni va parolingizni kiriting';


    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    TextInput::make('phone')
                        ->autofocus()
                        ->required()
                        ->placeholder('Telefon raqam')
                        ->label('Phone')
                    ->mask('99 999-9999'),
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->placeholder('Parol')
                        ->label('Parol'),
                    Checkbox::make('remember')
                        ->label('Eslab qolish'),
                ]
            )
            ->columns(1);
    }

    public function getHeading(): string|Htmlable
    {
        return $this->heading;
    }

    public function getTitle(): string|Htmlable
    {
        return self::$title;
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'phone' => $data['phone'],
            'password' => $data['password'],
        ];
    }
}