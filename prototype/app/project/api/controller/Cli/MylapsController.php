<?php
class Cli_MylapsController extends Base_Controller_Action{
    protected $oMylaps;
	protected $oRace;
    
	public function init()
	{
		parent::init();
		$this->oMylaps = new Xrace_Mylaps();
		$this->oRace = new Xrace_Race();
	}
    
    public function timingAction()
    {
        $Text = date("Y-m-d H:i:s",time()).":Start To Process\n";
        $filePath = __APP_ROOT_DIR__."log/Timing/";
        $fileName = date("Y-m-d",time()).".log";
        //写入日志文件
        Base_Common::appendLog($filePath,$fileName,$Text);

		$RaceId = $this->request->RaceId;
        $Force = $this->request->Force;
		if($RaceId)
		{
			$this->oMylaps->genMylapsTimingInfo($RaceId,$Force);
		}
		else
		{
			$RaceList = $this->oRace->getRaceList(array("inRun"=>1,"ToProcess"=>1),"RaceId,ToProcess");
			foreach($RaceList as $RaceId => $RaceInfo)
			{
                $Text = date("Y-m-d H:i:s",time()).":Start To Process RaceId:".$RaceId."\n";
                $filePath = __APP_ROOT_DIR__."log/Timing/";
                $fileName = date("Y-m-d",time()).".log";
                //写入日志文件
                Base_Common::appendLog($filePath,$fileName,$Text);
			    $this->oMylaps->genMylapsTimingInfo($RaceId,$RaceInfo['ToProcess'],0);
			}
		}

		//php.exe d:\xamppserver\htdocs\xrace_main\prototype\app\project\api\html\cli.php "ctl=mylaps&ac=timing&RaceId=25"
    }
}