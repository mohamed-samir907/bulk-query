# Bulk Query

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mosamirzz/bulk-query.svg?style=flat-square)](https://packagist.org/packages/mosamirzz/bulk-query)
[![Total Downloads](https://img.shields.io/packagist/dt/mosamirzz/bulk-query.svg?style=flat-square)](https://packagist.org/packages/mosamirzz/bulk-query)

Perform Bulk/Batch Update/Insert/Delete with laravel.

## Problem
I tried to make bulk update with laravel but i found that Laravel doesn't support that; so i created this package through it you can perform Batch/Bulk Update, Insert and delete.

## Installation

You can install the package via composer:

```bash
composer require mosamirzz/bulk-query
```

## Usage

> Bulk or batch update  meaning: that you can update multiple records in single query. Same for delete and insert (can insert or delete multiple records in single query).

The package provided you with 4 classes:
- `Insert::class` Used to insert multiple records in single query.
- `Update::class` Used to update multiple records in single query.
- `InsertOrUpdate::class` Used to insert multiple records in single query, and when duplicate record found in the records it will update it.
  - This query updates the records only when it find a `unique` or `primary key`. meaning that if the table doesn't have `unique` or `primary key` column and tried to insert record twice it will inserted as 2 records and will not update the record.
  - note: the `primary key` sholud sent to the query to determine by it if the query will update the duplicate records or not. if it will not send and `unique` column not sent to the query then the query will perfom insert only.
- `Delete::class` Used to delete multiple records in single query.

### Bulk Delete:
You can perfrom Bulk or batch delete by using the class `Delete::class` as following:

```php
use Mosamirzz\BulkQuery\Delete;

// 1. create new instance and pass the table name to the constructor.
$delete = new Delete("users");
// 2. send the `IDs` of the records to the prepare as array.
$delete->prepare([1, 2, 3, 4]);
// 3. execute the bulk delete query.
$delete->execute();
```

As shown above you can the default of delete statment is delete by the column `id` but if you want to perform the delete query with different column you can use the method `useKey` and pass to it the column name as following:

```php
use Mosamirzz\BulkQuery\Delete;

$delete = new Delete("users");
// change the default column used by the delete statment.
$delete->useKey("email");
// pass the values of the key that we want to delete it's records.
$delete->prepare(["gm.mohamedsamir@gmail.com", "user1@test.com", "user2@test.com"]);
$delete->execute();
```

> The default `key` used in the delete is the column `id`

### Bulk Insert:
You can perform bulk insert by using the class `Insert::class` as following:

```php
use Mosamirzz\BulkQuery\Insert;

// 1. create new instance and pass the table name.
$insert = new Insert("users");
// 2. pass the columns used by the insert query.
$insert->useColumns(["name", "email", "password"]);
// 3. send the records that we want to insert.
$insert->prepare([
    [
        "name" => "mohamed samir",
        "email" => "gm.mohamedsamir@gmail.com",
        "password" => "123456"
    ],
    [
        "name" => "user 1",
        "email" => "user1@test.com",
        "password" => "123456"
    ],
]);
// 4. execute the bulk insert query.
$insert->execute();
```

As shown above the `prepare` method accepts `array[]` each array represents the record that we want to insert in the database.

If you are trying to send record that exists before in the table, then the query will throw an exception like the following:
`SQLSTATE[23000]: Integrity constraint violation`

### Bulk Update:
You can perform bulk update by using the class `Update::class` as following:

```php
use Mosamirzz\BulkQuery\Update;

// 1. create instance and pass the table name.
$update = new Update("users");
// 2. pass the columns that we need to update them.
$update->useColumns(["name", "password"]);
// 3. pass the records that we need to update them
$update->prepare([
    1001 => [
        "name" => "mohamed samir updated",
        "password" => "1234_updated"
    ],
    1105 => [
        "name" => "user updated",
        "password" => "4321_updated"
    ],
]);
// 4. execute the bulk update query.
$update->execute();
```

As shown above
- the `prepare` method accepts `array[]` each array represents the record that we want to update in the database.
- each record must be like `"key" => [...]` the `key` represent the column name we use to updatte the record. in the example above the key it's default is `id` so when we want to update the record we must send the record `id` as the `key` and `array` of values that we want to update.

You can change the default column `id` used by the update to another column with `useKey` method.

for example you can use the `email` column as the key used by update as the following:
```php
use Mosamirzz\BulkQuery\Update;

$update = new Update("users");
$update->useColumns(["name", "password"]);
// change the default key used by the update.
$update->useKey("email");
$update->prepare([
    "gm.mohamedsamir@gmail.com" => [
        "name" => "mohamed samir updated",
        "password" => "mohamed"
    ],
    "user1@gmail.com" => [
        "name" => "user 1",
        "password" => "123456"
    ],
]);
$update->execute();
```

As shown above we used `email` column as the key used in update statement.

> The default `key` used in the update is the column `id`

If you have a record that you want to update only one column and set the other columns values to old value in the database then you can remove the `key=>value` of the column from the array as following:

```php
$update = new Update("users");
$update->useColumns(["name", "password", "is_admin"]);
$update->prepare([
    1 => [
        "is_admin" => true,
    ],
    3 => [
        "name" => "ahmed",
        "is_admin" => false,
    ],
    70 => [
        "password" => "123123"
    ],
]);
$update->execute();
```

As shown above the user with `id` = 1 will update the column `is_admin` only, we don't need to pass the `name` and `password` columns to it, the query will put the old value of `name` and `password` for the user with `id` = 1.

Same for the user with `id` = 3 the columns that will be updated is `name` and `is_admin` but the `password` column will be the old value.

Also the user with `id` = 70 only `password` will be updated.

### Bulk Insert or Update:
You can perform bulk insert and update in the same query by using the class `InsertOrUpdate::class`.

> The query updates the record only when it find a `unique` column or `primary key` column.

You can use the class as the following:

```php
use Mosamirzz\BulkQuery\InsertOrUpdate;

// 1. create instance and pass the table name.
$query = new InsertOrUpdate("users");
// 2. pass the columns used in insertion.
$query->useColumns(["name", "email", "password"]);
// 3. pass the columns used in the update when it find duplicate record.
$query->updatableColumns(["name", "password"]);
// 4. pass the records that we need to insert or update.
$query->prepare([
    [
        "name" => "mohamed samir",
        "email" => "gm.mohamedsamir@gmail.com",
        "password" => "mohamed124"
    ],
    [
        "name" => "hello",
        "email" => "hello@user.com",
        "password" => "hello"
    ],
]);
// 5. execute the bulk insert or update query.
$query->execute();
```

As shown above
- suppose that the user with `email` = `gm.mohamedsamir@gmail.com` is exists in the table before. 
- So when the query executes the record that has `email` = `gm.mohamedsamir@gmail.com` will be updated but the only columns `name` and `password` are updated.
- The second record that has `email` = `hello@user.com` will be inserted to the table because it's not exists before.


## Testing

```bash
composer run test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email gm.mohamedsamir@gmail.com instead of using the issue tracker.

## Credits

-   [Mohamed Samir](https://github.com/mosamirzz)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
