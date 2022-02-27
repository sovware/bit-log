# Bit Log
A debuggin tool for WordPress.

## How to use it
Adding the logs
```php
BitLog()->push( 'test-1', '123', __FILE__, __LINE__ );
BitLog()->push( 'test-2', '123', __FILE__, __LINE__ );
```

See the debug logs to this URL 
```
example.com/wp-json/bit-log/v1/logs
```

You can filter the logs by group following way
```
example.com/wp-json/bit-log/v1/logs?group=test-1
```