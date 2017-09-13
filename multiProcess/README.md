## 通用多进程模型

### 模型1

#### 场景
只需要子进程工作，不限制最大进程数

#### 使用方法

```php
$model = new Model_1();
for ($i = 0; $i < 100; $i++) {
    $model->run(new Demo_1($i));
}
```

#### 注意事项
Demo_1 实现 Worker 接口，需要有一个 run 方法由多进程 model 来调用

#### 说明
model 1 采用两次fork原理，避免产生僵尸进程问题

### 模型2

#### 场景
- 限制并发规模，超过时等待回收
- 所有任务完成后，主进程才退出

#### 使用方法

```php
$model = new Model_2(10, 'app\services\MultiProcess\Demo\Demo_1');
for  * ($i = 0; $i < 100; $i++) {
     $model->push($i);
}
$model->run();
$model->waitStop();
```

#### 注意事项
- Demo_1 实现 Worker 接口，需要有一个 run 方法由多进程 model 来调用
- 执行统计前需要将所有 task 配置 push 到 model 的任务队列
- 开始执行后，model 会 fork 新的子进程，实例化 Demo_1 并将配置传入构造函数

#### 说明
model 2 采用 wait 函数来控制并发规模，当子进程个数超过规定个数时，会等待工作中的子进程退出

###模型3

#### 场景
满足分布式并发任务

#### 说明
使用 web_socket 开发，正在进行最后调试
