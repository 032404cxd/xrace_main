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
			    $this->oMylaps->genMylapsTimingInfo($RaceId,$RaceInfo['ToProcess']);

			}
		}

		//php.exe d:\xamppserver\htdocs\xrace_main\prototype\app\project\api\html\cli.php "ctl=mylaps&ac=timing&RaceId=25"
    }
}