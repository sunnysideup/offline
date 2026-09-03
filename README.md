# This repo contains a generic offline page


## tl;dr

1. copy `offline.php` to your `public` dir.

Change the `public/index.php` to include this near the top:

```php
// your ip address
$ipaddresses = [
    'xxx.yyy.zzz.aaa',
];

$isVisitorFromOtherIp = isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'] ?? '', $ipaddresses, true);
if (!$isVisitorFromOtherIp) {
    require_once('./offline.php');
    die('');    
}

unset($ipaddresses);
unset($isVisitorFromOtherIp);
```

2. Make sure to set your IP address.

3. Definitely make sure to remove the code once done!
