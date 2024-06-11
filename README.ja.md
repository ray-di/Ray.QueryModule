# Ray.QueryModule
[![codecov](https://codecov.io/gh/ray-di/Ray.QueryModule/branch/1.x/graph/badge.svg?token=60G2MFDOBR)](https://codecov.io/gh/ray-di/Ray.QueryModule)
[![Type Coverage](https://shepherd.dev/github/ray-di/Ray.QueryModule/coverage.svg)](https://shepherd.dev/github/ray-di/Ray.QueryModule)
![Continuous Integration](https://github.com/ray-di/Ray.QueryModule/workflows/Continuous%20Integration/badge.svg)

[English](README.md)

## 概要

`Ray.QueryModule`はデータベースなど外部メディアへの問い合わせを、インジェクトされる関数オブジェクトで行うようにします。

 * `SqlQueryModule`はDB用です。SQLファイルをそのSQLを実行する単純な関数オブジェクトに変換します。
 * `WebQueryModule`はWeb API用です。URIをそのURIにWebレクエストする単純な関数オブジェクトに変換します。
 * `PhpQueryModule`は汎用のモジュールです。静的な変換では提供できないストレージアクセスをPHPの関数オブジェクトとして提供します。

## モチベーション

 * コードにドメイン層（利用コード）とインフラストラクチャ層（インジェクトされる関数）の明確な境界を持たせることが出来ます。
 * 実行オブジェクトは自動で生成されるので実行のための手続きコードを記述する必要がありません。
 * 利用コードは外部メディアの実態に無関心なので、ストレージを後で変更することができます。平行開発やスタブ化が容易です。


## インストール

### Composerインストール

    composer require ray/query-module

### モジュールのインストール

```php
use Ray\Di\AbstractModule;
use Ray\Query\SqlQueryModule;

class AppModule extends AbstractModule
{
protected function configure()
{
        // SqlQueryModule インストール
        $this->install(new SqlQueryModule($sqlDir));

        // WebQueryModuleインストール
        $webQueryConfig = [
            'post_todo' => ['POST', 'https://httpbin.org/todo'],
            'get_todo' => ['GET', 'https://httpbin.org/todo']
        ];
        $guzzleConfig = [];
        $this->install(new WebQueryModule($webQueryConfig, $guzzleConfig));
        
        // ISO8601 DateTimeフォーマット
        $this->>install(new Iso8601FormatModule(['created_at', 'updated_at']);
    }
}
```

### SQLファイル

$sqlDir/**todo_insert.sql**

```sql
INSERT INTO todo (id, title) VALUES (:id, :title)
```

$sqlDir/**todo_item_by_id.sql**

```sql
SELECT * FROM todo WHERE id = :id
```

## 利用

## SQLを実行オブジェクトに

`SqlQueryModule`をインストールするとSQLのファイル名によって束縛されたSQL実行関数がインジェクトされます。
例えば以下の例なら、`todo_insert.sql`ファイルが`$createTodo`の実行オブジェクトに変換されインジェクトされます

```php
class Todo
{
    public function __construct(
        private #[Sql("createTodo") QUereInterface $createTodo、
        private #[Sql("createTodo") QUereInterface $todo
    ){}
    
    public function get(string $uuid)
    {
        return ($this->todo)(['id' => $uuid]);
    }

    public function create(string $uuid, string $title)
    {
        ($this->createTodo)([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}
```
## 行または行リスト

RowInterface` または `RowListInterface` を使用すると、戻り値の型を `Row` または `RowList` に指定することができます。
RowInterface` は単一の行を返す SQL を指定するのに便利です。

```php
use Ray\Query\RowInterface;

class Todo
{
    public function __construct(
        #[Sql('todo_item_by_id') RowInterface $todo)
    {
        $this->todo = $todo;
    }

    public function get(string $uuid)
    {
        $todo = ($this->todo)(['id' => $uuid]); // 一行分のデータ
    }
}
```

```php
use Ray\Query\RowListInterface;

class Todos
{
    public function __construct(
        #[Sql("todos") private readonly RowListInterface $todos
    ){}
    
    public function get(string $uuid)
    {
        $todos = ($this->todos)(); // 複数行のデータ
    }
}
```

## メソッドをオーバーライドする

メソッド全体を `#[Query]` で指定した callable オブジェクトでオーバーライドすることができます。

```php
use Ray\Query\Annotation\Query;class Foo
{
    #[Query("todo_item_by_id")]
    public function get(string $id)
    {
    }
}
```

パラメータ名がメソッドの引数とQueryオブジェクトの引数で異なる場合は、uri_template式で解決できます。

```php
use Ray\Query\Annotation\Query;class FooTempalted
{
    #[Query(id:"todo_item_by_id?id={a}", templated:true)]
    public function get(string $a)
    {
    }
}
```

単一行の時は`type='row'`を指定します。

```php
use Ray\Query\Annotation\Query;class FooRow

#[Query("ticket_item_by_id", type: "row")]
public function onGet(string $id)： static
{
}
}
```

SELETした結果が無い場合にはcode 404が返ります。

## URI を Web リクエストオブジェクトに変換

`WebQueryModule`は設定で束縛したURIをWebアクセスする実行関数がインジェクトされます。
例えば以下の例なら、`https://httpbin.org/todo`を`POST`リクエストする`$createTodo`の実行オブジェクトに変換されインジェクトされます

```php
use Ray\Di\AbstractModule;
use Ray\Query\SqlQueryModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        // WebQueryModuleインストール
        $webQueryConfig = [
            'todo_post' => ['POST', 'https://httpbin.org/todo'], // bind-name => [method, uri]
            'todo_get' => ['GET', 'https://httpbin.org/todo']
        ];
        $guzzleConfig = []; // @see http://docs.guzzlephp.org/en/stable/request-options.html
        $this->install(new WebQueryModule($webQueryConfig, $guzzleConfig));
    }
}
```

使い方は `SqlQueryModule` と同じです。

```php
    public function __construct(
        callable $createTodo,
        callable $todo
    ){
        $this->createTodo = $createTodo;
        $this->todo = $todo;
  }
```

```php
// POST
($this->createTodo)([
    'id' => $uuid,
    'title' => $title
]);

// GET
($this->todo)(['id' => $uuid]);
```

`@Query`の利用コードも変わりません。

## PHPクラスを束縛

複数のクエリーを実行したり、他の依存が必要な場合にはPHPクラスに束縛し依存を利用します。

```php
class CreateTodo implements QueryInterface
{
    public function __construct(
        private PdoInterface $pdo,
        private QueryBuilderInferface $builder
    ) {
        $this->pdo = $pdo;
        $this->builder = $builder;
    }

    public function __invoke(array $query)
    {
        // $pdoと$builderを使ったクエリ実行
        return $result;
    }
}
```

```php
$this->bind(QueryInterface::class)->annotatedWith('cretate_todo')->to(CreateTodo::class);
```

利用コードは同じです。`#[Query]`の利用コードも変わりません。

## ISO8601 DateTimeモジュール

指定したコラム名の値を[ISO8601](https://www.iso.org/iso-8601-date-and-time-format.html)形式に変換します。PHPでは[DateTime::ATOM](https://www.php.net/manual/ja/class.datetime.php#datetime.constants.atom)の定数で定義されているフォーマットです。
日付のコラム名を配列にして`Iso8601FormatModule`の引数に渡してインストールします。

```php
$this->install(new Iso8601FormatModule(['created_at', 'updated_at']));
```
## SQL file name log

SQLファイル名をコメントとしてSQL文に付加する事ができます。クエリーログに有用です。

```php
use Ray\Query\SqlFileName;
use Ray\Query\SqlQueryModule;

$this->install(new SqlQueryModule(__DIR__ . '/Fake/sql', null, new SqlFileName()));
```

実行SQL

```sql
/* todo_item_by_id.sql */ SELECT * FROM todo WHERE id = :id
```

## デモ

```
php demo/run.php
```

## BEAR.Sunday

 * [Koriym.Ticketsan](https://github.com/koriym/Koriym.TicketSan/blob/master/src/Resource/App/Ticket.php)

