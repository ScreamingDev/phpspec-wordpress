# Make proper WordPress classes with PHPSpec

```
composer require --dev sourcerer-mike/phpspec-wordpress:dev-master
```


## Example

```
./bin/phpspec describe:wp Widgets\\The_Banana
./bin/phpspec run
```

Will result in a file called "includes/widgets/class-the-banana.php":

```
<?php

namespace Widgets;

class The_Banana {

}
```