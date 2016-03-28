<?php
/**
 * 地图相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Map extends Base_Widget
{
	//声明所用到的表
	protected $table = 'user_profile';

	public function getQxTrace($RaceId,$UserId,$StartTime=1457666940 ,$EndTime=1457668740)
	{
		echo date("Y-m-d H:i:s",$StartTime)."<br>";
		echo date("Y-m-d H:i:s",$EndTime)."<br>";
		$url = "http://api.map.baidu.com/trace/v2/track/gethistory?ak=22b695506ee991ab96d36152247f5092&service_id=111794&start_time=$StartTime&end_time=$EndTime&entity_name=866696020399042";
		$return = file_get_contents($url);
		$return  = json_decode($return,true);
		if(isset($return['points']))
		{
			krsort($return['points']);
			foreach($return['points'] as $key => $pointInfo)
			{
				echo date("Y-m-d H:i:s",$pointInfo['loc_time'])."-".$pointInfo['create_time']."<br>";
			}
		}
		print_R($return);
	}

}
