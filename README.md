# yii2-multi-process
common multiple process model for yii2

### How to use it

- [Posix](http://php.net/manual/en/book.posix.php) extension for php is needed
- only for Unix-based system(such as Ubuntu, MacOS)
- then put the multiProcess directory into your yii2's component directory

### Model_1

#### Situation
a simple model, only need it works in multi process

####  Demo

```php
$model = new Model_1();
for ($i = 0; $i < 100; $i++) {
    $model->run(new Demo_1($i));
}
```

#### Notes
the Demo_1 is your worker class which should implement Worker interface with a run() function in it

#### other
Model_1 forks twice so as to avoid zombie process

### Model_2

#### Situation
- limit the number of sub-processes, when it is up to the maximum, the parent process will wait
- the parent process will exit until all its sub-process has exited already

#### Demo

```php
$model = new Model_2(10, 'app\services\MultiProcess\Demo\Demo_1');
for ($i = 0; $i < 100; $i++) {
     $model->push($i);
}
$model->run();
$model->waitStop();
```

#### Notes
- the Demo_1 is supposed to implement Worker interface which contains a run() function in it
- before you call run() function, you should push all tasks into list with the push() function
- after call run() function, the model will fork sub-process and pop a task from the list to the demo's construction function

#### other
Model_2 use Unix's wait() function to limit the number of sub-process, when it is up to the maximum, the parent process will wait

### Model_3

#### Situation
this model will satisfy the distribution demanding, but it is being developed.


#### Notes
developed by web_socket, and it will be coming soon.
