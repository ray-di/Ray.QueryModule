# Ray.QueryModule

Ray.QueryModule converts SQL string into invokable DB objects.

## Installation

### Composer install

    $ composer require ray/query-module 1.x-dev
 
### Module install

```php
use Ray\Di\AbstractModule;
use Ray\Query\SqlQueryModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new SqlQueryModule($sqlDir));
    }
}
```

### SQL files

$sqlDir/todo_insert.sql

```sql
INSERT INTO todo (id, title) VALUES (:id, :title)
```

$sqlDir/todo_item_by_id.sql 

```sql
SELECT * FROM todo WHERE id = :id
```

SQL file name has the convention.

```
{symbol}_{item}_{description}.sql
```

 * `symbol` is target ID, whcih can be table name of resource name. ex) 'users', 'entries'
 * `item` is fixed string option when the result of query expected to be single line.
 * `desciption` is option for the description. ex) 'most_stard_in_last_weeek'

ex) `entries_item_by_id.sql`, `entries_popular_in_last_weeek.sql`

### Usage

```php
class Todo
{
    /**
     * @var callable
     */
    private $todoInsert;
    
    /**
     * @var callable
     */
    private $todoItem;
    
    /**
     * @Named("todoInsert=todo_insert, todoItem=todo_item_by_id")
     */
    public function __construct(
        callable $todoInsert,
        callable $todoItem
    ){
        $this->todoInsert = $todoInsert;
        $this->todoItem = $todoItem;
    }
    
    public function get(string $uuid)
    {
        return ($this->todoItem)(['id' => $uuid])[0];
    }

    public function create(string $uuid, string $title)
    {
        ($this->todoInsert)([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}
```

## Demo

```
php demo/run.php
```
