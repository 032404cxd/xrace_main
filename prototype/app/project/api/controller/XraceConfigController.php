<?php
/**
 *
 * 
 */
class XraceConfigController extends AbstractController
{
    /**
     *对象声明
     */
    protected $oRace;
    /**
     * 初始化
     * (non-PHPdoc)
     * @see AbstractController#init()
     */
    public function init()
    {
        parent::init();
        $this->oRace = new Xrace_Race();
    }
    public function indexAction() {
        echo 'index';
    }
    
    /**
     *获得所有的RaceCatalogList
     */
    public function getRaceCatalogListAction()
    {
        $raceCatalogList = $this->oRace->getAllRaceCatalogList();
        if(!is_array($raceCatalogList)) 
        {
           $raceCatalogList = array();
        }
        $result = array("return"=>0,"raceCatalogList"=>$raceCatalogList);
        echo json_encode($result);
    }
    
    public function functionName($param) {
        
        
    }

}