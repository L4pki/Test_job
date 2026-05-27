<?php

namespace App\Orchid\Layouts\Posts;

use App\Models\Post;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PostListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'posts';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')->defaultHidden(),
            TD::make('title', 'Title'),
            TD::make('text', 'Text')
                ->render(function (Post $post) {
                    $shortText = Str::limit($post->text, 50);
                    $fullText = htmlspecialchars($post->text);
                    return "<span title='{$fullText}' style='cursor: help;'>{$shortText}</span>";}),
            TD::make('author_id', 'ID Author'),
            TD::make('actions', 'Действия')
            ->render(function (Post $post) {
                $editButton = ModalToggle::make('Редактировать')
                    ->modal('updatePost')
                    ->method('update')
                    ->modalTitle('Редактирование поста')
                    ->asyncParameters(['post' => $post->id])
                    ->icon('pencil');
                $deleteButton = ModalToggle::make('Удалить')
                    ->modal('deletePost')
                    ->method('delete')
                    ->modalTitle('Вы точно хотите удалить пост?')
                    ->asyncParameters(['post' => $post->id])
                    ->icon('trash');
                return $editButton . ' ' . $deleteButton;
            }),
        ];
    }
}
