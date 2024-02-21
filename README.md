# The PHP framework that gets out of your way.

Read how to get started with Tempest [here](https://github.com/tempestphp/tempest-docs/blob/master/01-getting-started.md).

Zero config, zero overhead. This is Tempest:

```php
final readonly class BookController
{
    #[Get('/blog')]
    public function index() { /* … */ }
    
    #[Get('/blog/{post}')]
    public function show(Post $post) { /* … */ }
}

final readonly class RssSyncCommand
{
    public function __construct(private Console $console) {}

    #[ConsoleCommand('rss:sync')]
    public function __invoke(bool $force = false)  { /* … */ }
}
```