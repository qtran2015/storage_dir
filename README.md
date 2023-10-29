# storage_dir
Laravel package - to create new directory in the storage directory

# install
```
composer require qtran2015/storage-dir
```

# add to config/app.php Application Service Providers
```
Qtran2015\StorageDir\StorageDirProvider::class
```

# clear cache
```
php artisan optimize:clear
```

# usage response statuses
exists | created | failed

# default read execute
```
$response = \Qtran2015\StorageDir\StorageDir::setDir('test1/dir_1/dir_2')->execute();
```

# read write execute
```
$response = \Qtran2015\StorageDir\StorageDir::setDir('test2/dir_1/dir_2')
        ->fullAccess()
        ->execute();
```

# remove full access from a directory from provided path
```
$response = \Qtran2015\StorageDir\StorageDir::setDir('test2/dir_1/dir_2/dir_3')
        ->removeFullAccessDir('dir_1')
        ->execute();
```

# give full access to a directory from provided path
```
$response = \Qtran2015\StorageDir\StorageDir::setDir('test1/dir_1/dir_2')
        ->giveFullAccessDir('dir_2')
        ->execute();
```
