# sprits-php

Smallish webframework for/by/in php.

```
./framework <- this is where the framework lives.
./framework/sprits.php <- this is the webframework file, include it in all your page to unlock its powers.
./template <- this is a template project.
```

To enjoy all benefits of this framework, have Apache rewrite all requests for you. (I.e you wont need to extract your own routes.)
Something like this:

```
RewriteEngine on

# if a directory or a file exists, use it directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# otherwise forward it to index.php
RewriteRule . template/index.php
```