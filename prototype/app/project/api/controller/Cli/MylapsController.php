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
		if($RaceId)
		{
			echo "RaceId:".$RaceId."\n";
			$this->oMylaps->genMylapsTimingInfo($RaceId);
		}
		else
		{
			$RaceList = $this->oRace->getRaceList(array("inRun"=>1),"RaceId");
			foreach($RaceList as $RaceId => $RaceInfo)
			{
				echo "RaceId:".$RaceId."\n";
				$this->oMylaps->genMylapsTimingInfo($RaceId);
			}
		}

		//print_R($RaceList);
		//$this->oMylaps->genMylapsTimingInfo($RaceId);
		//php.exe d:\xamppserver\htdocs\xrace_main\prototype\app\project\api\html\cli.php "ctl=mylaps&ac=timing&RaceId=25"
    }
}