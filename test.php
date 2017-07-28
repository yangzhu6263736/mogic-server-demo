<?php
define('BASE_PATH', dirname(__FILE__));
define('MOG_PATH', dirname(__FILE__).'/mogic/');
define('APP_PATH', dirname(__FILE__).'/App/');
include_once(MOG_PATH."index.php");
Mogic\Server::createServer('localhost', 3736);
echo "xxx";

// $serv = new Swoole\Http\Server("localhost", 3736, SWOOLE_PROCESS);
// $serv->set(array(
//     'worker_num' => 3,
//     'dispatch_mode'	=>	1,//与worker通信模式 1轮询
//     // 'task_worker_num' => 1,
// //    'task_ipc_mode' => 3,
// //    'message_queue_key' => 0x70001001,
//     //'task_tmpdir' => '/data/task/',
// ));

// $serv->on('Start', function($serv) {
//     swoole_set_process_name("php JoySwoole master Process");
// });

// $serv->on('Request', function($request, $response) {
//     // var_dump($request->get);
//     // var_dump($request->post);
//     // var_dump($request->cookie);
//     // var_dump($request->files);
//     // var_dump($request->header);
//     // var_dump($request->server);
//     global $serv;
// 	echo "Request".$serv->worker_pid."\n";
//     $response->cookie("User", "Swoole");
//     $response->header("X-Server", "Swoole");
//     $response->end("<h1>Hello Swoole!</h1>");
// });
// $serv->on('WorkerStart', function ($serv, $workerId){
//     if($workerId >= $serv->setting['worker_num']) {

//         swoole_set_process_name("php JoySwoole task worker");
//     } else {
//         // swoole_set_process_name("php JoySwoole event worker");
//         include("JoyEngine/Process/WorkerProcess.php");
//         new JoyEngine\Process\WorkerProcess($serv, $workerId);
//     }
// });
// $serv->on('ManagerStart', function ($serv){
//     echo "on ManagerStart\n";
//     swoole_set_process_name("phpJoySwoole Manager Porcess");
// });
// $serv->start();
