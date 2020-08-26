//php-pcntl模块下的：

pcntl_signal(SIGCHLD, SIG_IGN);
 
$times = 200;
while ($times-- > 0) {
    $pid = pcntl_fork();
    if ($pid > 0) {
    } else {
        order();
        die;
    }
}
sleep(20);
function order() {
    $conn = new Redis;
    //connect redis
    $conn->connect("127.0.0.1", 6379);
 
    do {
        //watch inventory
        $conn->watch('inventory');
        $inventory = $conn->get('inventory');
        //由于本地服务过访问过快。所以休息50毫秒真实模拟高并发
        usleep(50000);
        if ($inventory <= 0) {
            echo "抢购失败！";
            break;
        }
        $conn->multi();
        $conn->decr("inventory");
 
    } while ($conn->exec());
 
    die;
}
