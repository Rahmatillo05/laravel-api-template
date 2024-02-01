<?php

namespace App\Providers\Filament\Pages;

use App\Http\Repositories\AuthRepository;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;
use function __;
use function app;
use function array_key_exists;
use function ceil;
use function event;
use function filament;
use function session;

class Register extends \Filament\Pages\Auth\Register
{

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    $this->getPhoneComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ]
            );
    }

    public function getWizardSteps(): array
    {
        return [
            Wizard::make([
                Wizard\Step::make('Telefon raqam')
                    ->schema([
                        TextInput::make('phone')
                            ->autofocus()
                            ->required()
                            ->placeholder('Telefon raqam')
                            ->label('Telefon raqam')
                            ->mask('99 999-9999'),
                    ]),
                Wizard\Step::make('SMS Tasdiqlash')
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->placeholder('SMS Tasdiqlash')
                            ->label('SMS Tasdiqlash')
                            ->mask('999999'),
                    ]),
                Wizard\Step::make('Parol yaratish')
                    ->schema([
                        TextInput::make('password')
                            ->required()
                            ->placeholder('Parol yaratish')
                            ->label('Parol yaratish')
                            ->type('password'),
                        TextInput::make('password_confirmation')
                            ->required()
                            ->placeholder('Parolni tasdiqlash')
                            ->label('Parolni tasdiqlash')
                            ->type('password'),
                    ]),
            ])
        ];
    }

    public function getHeading(): string|Htmlable
    {
        return "Ro'yxatdan o'tish";
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label('Kirish')
            ->url(filament()->getLoginUrl());
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        $data['password'] = Hash::make($data['password']);
        $data['phone'] = (new AuthRepository())->sanitizePhone($data['phone']);

        $user = User::create($data);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    private function getPhoneComponent()
    {
        return TextInput::make('phone')
            ->autofocus()
            ->required()
            ->placeholder('Telefon raqam')
            ->label('Telefon raqam')
            ->maxLength(9)
            ->integer()
            ->unique('users', 'phone')
            ->prefix('+998');
    }


}
