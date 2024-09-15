# Visitor Tracking System for CodeIgniter Shield
A lightweight visitor tracking system for CodeIgniter Shield, designed to monitor user profile visits similar to LinkedIn’s profile visitor feature.

# Install
Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities and always be up-to-date:
```
composer require aselsan/visitors
```

Once the files are downloaded and included in the autoload, run any library migrations to ensure the database is set up correctly:
```
php spark migrate --all
```

# Configuration
Add `HasVisitors` trait to your users model and initialize visitors with `initVisitors()` method.

```php
class ExampleUsersModel extends BaseModel
{
    use HasVisitors;

    // ...

    protected function initialize()
    {
        $this->initVisitors();
    }

    // ...
}
```

And if you use Entity class, add `Visitable` trait to it:

```php
class ExampleUser extends Entity
{
    use Visitable;

    // ...
}
```

# Usage
```php
$users = auth()->getProvider();
$user  = $users->find($id);

//visit this user
$user->visit();

//get total visitors
$user->getSumVisitors();

//get visitors details
$user->visitors;
```

## License

This project is licensed under the MIT License - see the [LICENSE](/LICENSE) file for details.
