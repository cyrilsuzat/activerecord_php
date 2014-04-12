### Run the examples

**1. Create the database structure:**

```sql
CREATE TABLE `posts` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NULL,
  `body` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL
);
CREATE TABLE `comments` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL ,
  `author` VARCHAR(255) NULL,
  `content` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL
);
```

**2. Setup the `config.php` file**

**3. Run `main.php`**



### Notes

- Delete `DbTable::get_called_class()` if using PHP>=5.3.0
- Respect the following conventions:
  - Name `id` the column of the primary key
  - Foreign keys like `{foreign-table}_id` *(ex: post_id)*
  - Tables must contains `created_at` and `updated_at` cols
  - Params staring with underscore (_) don't go into DB
    ex: `new User(array('_create'=>'Create', 'name'=>'Joe'))`


### Credits

Created by [Cyril Suzat](http://cyrilsuzat.com)
