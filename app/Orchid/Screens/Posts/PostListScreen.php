<?php

namespace App\Orchid\Screens\Posts;

use App\Http\Requests\PostRequests\PublicationPostRequest;
use App\Models\Post;
use App\Orchid\Layouts\Posts\PostListTable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PostListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'posts' => Post::paginate(5),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Post';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Создать пост')
                ->modal('createPost')
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
            PostListTable::class,
            Layout::modal('createPost', [
                Layout::rows([
                    Input::make('post.title')
                        ->title('Название')
                        ->type('string')
                        ->set('minlength', '3')
                        ->set('maxlength', '100')
                        ->help('от 3, до 100!')
                        ->required(),
                    Input::make('post.text')
                        ->title('Текст')
                        ->type('string')
                        ->set('minlength', '10')
                        ->set('maxlength', '5000')
                        ->help('от 10, до 5000!')
                        ->required(),
                ])
            ])->title('Создание поста')
                ->applyButton('Сохранить')
                ->closeButton('Отмена'),
            Layout::modal('updatePost', [Layout::rows([
                Input::make('post.id')
                    ->disabled()
                    ->type('integer'),
                Input::make('post.title')
                    ->required()
                    ->title('Название')
                    ->set('minlength', '3')
                    ->set('maxlength', '100')
                    ->help('от 3, до 100!')
                    ->type('string'),
                Input::make('post.text')
                    ->required()
                    ->title('Текст')
                    ->set('minlength', '10')
                    ->set('maxlength', '5000')
                    ->help('от 10, до 5000!')
                    ->type('text'),
            ])])->title('Редактирование поста')
                ->applyButton('Обновить')
                ->closeButton('Отмена')
                ->async('asyncGetPost'),
            Layout::modal('deletePost', [])
                ->applyButton('Удалить')
                ->closeButton('Отменить')
        ];
    }

    public function create(PublicationPostRequest $request): void
    {
        $validated = $request->validated();
        $post = new Post();
        $post->title = $validated['title'];
        $post->text = $validated['text'];
        $post->author_id = auth()->id();
        $post->save();
        Toast::info('Пост успешно создан!');
    }

    public function asyncGetPost(Post $post): array
    {
        return [
            'post' => $post
        ];
    }

    public function update(PublicationPostRequest $request): void
    {
        $postId = $request->query('post');
        $post = Post::query()->find($postId);
        if (!$post) {
            Toast::error('Пост не найден');
            return;
        }
        $updateData = [
            'title' => $request->input('post.title'),
            'text' => $request->input('post.text'),
        ];
        $post->update($updateData);
        Toast::info('Пост успешно обновлен!');
    }

    /**
     * @throws \Exception
     */
    public function delete(Request $request): void
    {
        $postId = $request->query('post');
        $post = Post::query()->find($postId);
        if (!$post) {
            Toast::error('Пост не найден');
            return;
        }
        $post->delete();
        Toast::info('Пост удалён');
    }
}
