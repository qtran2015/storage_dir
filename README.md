# storage_dir
Laravel package - to create new directory in the storage directory

# install
composer require qtran2015/storage-dir

# add to config/app.php Application Service Providers
Qtran2015\StorageDir\StorageDirProvider::class

# clear cache
php artisan optimize:clear

# examples
use Qtran2015\StorageDir\StorageDir;

# default read execute
$response = StorageDir::setDir('test1/sub_dir1/sub_dir2')->execute();

# read write execute
$response = StorageDir::setDir('test2/sub_dir1/sub_dir2')->fullAccess()->execute();

# response statuses
exists | created | failed
