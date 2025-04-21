# mostly-static

Static site generation from an existing Laravel site.

## Warning

Pretty much don't use this directly... This package currently does everything that I needed it to do so it is unlikely I will further flesh this out. Instead, fork and adapt to your needs.

## Usage

Use the `static` macro on your routes to mark them as static. For example in `routes/web.php`:

```php
Route::get('/', [PageController::class, 'home'])->static();
Route::get('/about', [PageController::class, 'about'])->static();
// etc.
```

In order to handle route parameters, provide a generator that yields arrays of route parameters for each static page you wish to generate:

```php
class BlogPostParameterProvider
{
    public function __construct(private MarkdownLoader $loader)
    {}

    public function __invoke()
    {
        yield from $this->loader
            ->allPosts()
            ->map(fn (Post $post) => ['post' => $post->slug])
            ->all();
    }
}
```

And then back in `routes/web.php`:

```php
Route::get('/blog', [PostController::class, 'index'])->static();
Route::get('/blog/{post}', [PostController::class, 'show'])->static(BlogPostParameterProvider::class);
```

Finally, run `php artisan mostly-static:generate --output /path/to/desired/output/dir`. If omitted, output directory will default to the Laravel public path.
