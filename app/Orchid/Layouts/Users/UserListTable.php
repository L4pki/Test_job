<?php

namespace App\Orchid\Layouts\Users;

use App\Models\User;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class UserListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'users';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')->defaultHidden(),
            TD::make('name', 'Имя'),
            TD::make('email', 'Email'),
            TD::make('action', 'Действия')
                ->render(function (User $user) {
                    $editButton = ModalToggle::make('Редактировать')
                        ->modal('updateUser')
                        ->method('update')
                        ->modalTitle('Редактирование пользователя')
                        ->asyncParameters(['user' => $user->id])
                        ->icon('pencil');
                    $deleteButton = ModalToggle::make('Удалить')
                        ->modal('deleteUser')
                        ->method('delete')
                        ->modalTitle('Вы точно хотите удалить пользователя и все его посты?')
                        ->asyncParameters(['user' => $user->id])
                        ->icon('trash');
                    return $editButton . ' ' . $deleteButton;
                }),
        ];
    }
}
