<?php

namespace App\Orchid\Screens\Users;

use App\Http\Requests\UserRequests\RegistrationUserRequest;
use App\Models\User;
use App\Orchid\Layouts\Users\UserListTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'users' => User::paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Users';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Создать пользователя')
                ->modal('createUser')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            UserListTable::class,
            Layout::modal('createUser', [
                Layout::rows([
                    Input::make('user.name')
                        ->title('Имя')
                        ->set('minlength', '3')
                        ->set('maxlength', '100')
                        ->help('от 3, до 100!')
                        ->required(),
                    Input::make('user.email')
                        ->title('Email')
                        ->type('email')
                        ->required(),
                    Input::make('user.password')
                        ->title('Пароль')
                        ->type('password')
                        ->set('minlength', '5')
                        ->set('maxlength', '20')
                        ->help('от 5, до 20!')
                        ->required(),
                    CheckBox::make('user.is_admin')
                        ->title('Администратор')
                        ->sendTrueOrFalse()
                        ->help('Отметьте, чтобы дать права администратора'),
                ])
            ])->title('Создание пользователя')
                ->applyButton('Сохранить')
                ->closeButton('Отмена'),
            Layout::modal('updateUser', [Layout::rows([
                Input::make('user.id')
                    ->disabled()
                    ->type('string')
                    ->title('ID'),
                Input::make('user.email')
                    ->required()
                    ->title('Email'),
                Input::make('user.name')
                    ->required()
                    ->title('Имя')
                    ->set('minlength', '3')
                    ->set('maxlength', '100')
                    ->help('от 3, до 100!')
                    ->type('string'),
                Input::make('user.password')
                    ->title('Пароль')
                    ->type('password')
                    ->set('minlength', '5')
                    ->set('maxlength', '20')
                    ->help('от 5, до 20!'),
            ])])->title('Редактирование пользователя')
                ->applyButton('Обновить')
                ->closeButton('Отмена')
                ->async('asyncGetUser'),
            Layout::modal('deleteUser', [])
                ->applyButton('Удалить')
                ->closeButton('Отменить')
        ];
    }

    public function create(RegistrationUserRequest $request): void
    {
        $validated = $request->validated();
        $userData = $validated['user'] ?? $validated;
        $existingUser = User::query()->where('email', $userData['email'])->exists();
        if ($existingUser) {
            Toast::error('❌ Пользователь с таким email уже существует!');
            return;
        }
        $userData['password'] = bcrypt($userData['password']);
        if (isset($validated['is_admin']) && $validated['is_admin']) {
            $userData['permissions'] = [
                'platform.index' => true,
                'platform.systems.users' => true,
                'platform.systems.attachment' => true,
            ];
        } else {
            $userData['permissions'] = null;
        }
        unset($userData['is_admin']);
        User::query()->create($userData);
        Toast::info('Пользователь успешно создан!');
    }

    public function asyncGetUser(User $user): array
    {
        return [
            'user' => $user
        ];
    }

    public function update(Request $request): void
    {
        $userId = $request->query('user');
        $user = User::query()->find($userId);

        if (!$user) {
            Toast::error('Пользователь не найден');
            return;
        }
        $updateData = [
            'name' => $request->input('user.name'),
            'email' => $request->input('user.email'),
        ];
        if ($request->filled('user.password')) {
            $updateData['password'] = bcrypt($request->input('user.password'));
        }
        $user->update($updateData);
        Toast::info('Пользователь успешно обновлен!');
    }

    /**
     * @throws \Exception
     */
    public function delete(Request $request): void
    {
        $userId = $request->query('user');
        $user = User::query()->find($userId);
        $authUser = Auth::user();
        if (!$user) {
            Toast::error('Пользователь не найден');
            return;
        }
        if ($authUser->id === (int)$userId) {
            Toast::error('Нельзя удалить авторизованного пользователя!');
            return;
        }
        $user->delete();
        Toast::info('Пользователь удалён');
    }
}
