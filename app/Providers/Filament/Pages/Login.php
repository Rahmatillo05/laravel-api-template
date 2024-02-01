<?php

namespace App\Providers\Filament\Pages;

use App\Http\Repositories\AuthRepository;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use function __;
use function app;
use function array_key_exists;
use function ceil;
use function session;

class Login extends \Filament\Pages\Auth\Login
{
    public static ?string $title = 'Akkauntga kirish';

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
            'phone' => (new AuthRepository())->sanitizePhone($data['phone']),
            'password' => $data['password'],
        ];
    }
    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Kirish')
            ->submit('authenticate')
            ->button();
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label('Ro\'yxatdan o\'tish')
            ->url(filament()->getRegistrationUrl());
    }


    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
