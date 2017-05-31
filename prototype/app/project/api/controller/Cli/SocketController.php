<?php

class Cli_SocketController extends Base_Controller_Action
{
	public $oSocket;
    public $oSocketCli;
    public $oSocketQueue;
	
	public function init()
	{
		parent::init();
		$this->socketPath =  "/www/opt";
		//$this->oSocketServer = new Connect_SocketServer();
        $this->oSocketClient = new Xrace_Connect_SocketClient();
        //$this->oSocketQueue = new Config_SocketQueue();
	}
	
	function socketServerAction()
	{
		set_time_limit(0);
			$ipserver = '192.168.8.110';
			//$ipserver = '192.168.30.37';

			$SocketPort = 9999;
			$errno = 1;
			$timeout = 1;
			$buff = 1024;	//�����С
			echo "Server:".$ipserver.",Port:".$SocketPort."\n";
			$socket=stream_socket_server('tcp://'.$ipserver.':'.$SocketPort, $errno, $errstr);
			echo "socket:".$socket."\n";
			stream_set_blocking($socket,0);
			//���socket�Ѿ�����,����socket���Ӳ���ȡ��Ϣ
			while(true)
			{
				$conn = @stream_socket_accept($socket,-1);
				$Buff_to_process = "";
				while($conn)
				{		
					echo "conn:".$conn."\n";
					$buff = fread($conn,4096);
					echo "buff:".$buff."\n";
					$length = strlen($buff);
                    fwrite($conn,"ServerName@AckPong@Version2.1@$");
					if($length === 0)
					{
						echo "waiting";
						sleep(1);
					    //echo "unset";
					    //unset($conn);
						//break;
					}
					else
					{
						/*
					    $format="V2Length/vuType/V2Msg";
						if(!isset($Buff_to_process))
						{
							$Buff_to_process = "";
						}
						$Buff_to_process .= $buff;
						do
						{
							echo "here";
						    $unpack_buff =  @unpack($format,$Buff_to_process);
							$text = substr($Buff_to_process,0,$unpack_buff['Length1']);
							$unpackArry =  @unpack($format,$text);
                            echo $unpackArry['uType']."\n";
                            print_R($unpackArry);
							$Buff_to_process = substr($Buff_to_process,strlen($text),strlen($Buff_to_process)-strlen($text));
							echo "last:".strlen($Buff_to_process)."\n";
							$unpack2_buff = @unpack($format,$Buff_to_process);
						}
						while(($unpack2_buff['Length1'] <= strlen($Buff_to_process))&&($unpack2_buff['Length1']>0));
						*/
					}
					
				}
			}

	}
	function socketServerNewAction()
    {
        set_time_limit(10);
        $commonProtocol = getprotobyname("tcp");
        $socket = socket_create(AF_INET, SOCK_STREAM, $commonProtocol);
        if ($socket) {
            $result = socket_bind($socket, '192.168.8.110', 9999);
            if ($result) {
                $result = socket_listen($socket, 5);
                if ($result) {
                    echo "监听成功";
                }
            }
        }else{
            echo "监听失败";
        }
        do {
            if (($msgsock = socket_accept($socket))) { /* 发送提示信息给连接上来的用户 */
                $msg = "==========================================\r\n" .
                    "Welcome to the PHP Test Server. \r\n\r\n" .
                    "To quit, type 'quit'. \r\n" .
                    "To shut down the server type 'shutdown'.\r\n" .
                    "To get help message type 'help'.\r\n" .
                    "==========================================\r\n" .
                    "php>";
            }

            socket_write($msgsock, $msg, strlen($msg));

            do {
                $buf = socket_read($msgsock, 2048, PHP_BINARY_READ);

                if (false === $buf) {
                    echo "socket_read() failed: reason: " . socket_strerror($result) . "\n";
                    break 2;
                }
                if (!$buf = trim($buf)) {
                    continue;
                } /* 客户端输入quit命令时候关闭客户端连接 */
                if ($buf == 'q') {
                    break;
                } /* 客户端输入shutdown命令时候服务端和客户端都关闭 */
                if ($buf == 'shutdown') {
                    socket_close($msgsock);
                    break 2;
                } /* 客户端输入help命令时候输出帮助信息 */
                if ($buf == 'h') {
                    $msg = " PHP Server Help Message \r\n\r\n" .
                        " To quit, type 'quit'. \r\n" .
                        " To shut down the server type 'shutdown'.\r\n" .
                        " To get help message type 'help'.\r\n" .
                        "php> ";
                    socket_write($msgsock, $msg, strlen($msg));
                    continue;
                } /* 客户端输入命令不存在时提示信息 */
                $talkback = "PHP: unknow command '$buf'.\r\nphp> ";
                socket_write($msgsock, $talkback, strlen($talkback));
                echo "$buf\n";
            } while (true);
            socket_close($msgsock);
        }while (true);
        /* 关闭Socket连接 */
        socket_close($socket);
    }
    
    function socketClientAction()
    {
			echo date("Y-m-d H:i:s",time())."write connecting:\n";
			$connect = @fsockopen("127.0.0.1", 9999, $errno, $errstr, 1);
			$Buff_to_process = "";
			// stream_set_blocking($sock,TRUE);
			stream_set_timeout($connect,0);
			echo "connected:".$connect."\n"; 
			while(true)
			{
						$v = array("uType"=>"001","Length"=>18,"Msg"=>"Hello");
			            echo $v['uType']."\n";
                        $SendContent = $this->oSocketClient->PackTest($v);

						echo "connect:".$connect."\n";
						if($connect)
						{
							fwrite($connect,$SendContent);
						}
						else
						{
							fclose($connect);
							echo date("Y-m-d H:i:s",time()). "write connecting:";
							$connect = @fsockopen("127.0.0.1",99999, $errno, $errstr, 1);
							$Buff_to_process = "";
							// stream_set_blocking($sock,TRUE);
							stream_set_timeout($connect,0);
							echo "connected:".$connect."\n";                             	
						}
						sleep(1);
			}
    }
    
	function writeTxt($filename,$content)
	{
		$logpath = "/www/opt/sock/log/";
		$filename = $logpath.$filename;
		$fp = fopen($filename,'w');
		fwrite($fp,$content);
		fclose($fp);
	}
}