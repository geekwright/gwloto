<?php
/**
* gwlotoPrintJob.php - class to facilitate printing data
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2010 geekwright, LLC. All rights reserved. 
* @license    gwloto/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwloto
* @version    $Id$
*/

if (!defined("XOOPS_ROOT_PATH")) die("Root path not defined");

include_once ('include/common.php');
include_once ('include/jobstatus.php');
include_once ('include/seqoptions.php');

class gwlotoPrintJob {
	//------------------------------------------------------------
	// Properties
	//------------------------------------------------------------

	protected $currentjob;
	protected $currentplan;
	protected $currentseq;
	protected $language;

	protected $eachPointFunc;
	protected $beginStepFunc;
	protected $endStepFunc;
	protected $beginJobFunc;
	protected $endJobFunc;

	protected $jobdata;
	protected $jobsteps;

	//------------------------------------------------------------
	// Methods
	//------------------------------------------------------------

	/**
	* class constructor
	* 5 callbacks should expect parameters ($jobdata,$jobstepdata,$pointdata)
	* @param integer $jid job_id of job to process
	* @param integer $cpid cplan_id of jobsetp or 0 for all steps
	* @param integer $seq job sequence/phase (disconnect, inspect, reconnect)
	* @param mixed $lid language_id or and array of same
	* @param mixed $eachPointFunc callback function as name, array(object,method), or false
	* @param mixed $beginStepFunc callback function as name, array(object,method), or false
	* @param mixed $endStepFunc callback function as name, array(object,method), or false
	* @param mixed $beginJobFunc callback function as name, array(object,method), or false
	* @param mixed $endJobFunc callback function as name, array(object,method), or false
	* @access private
	* @since 1.0.0
	*/
	public function __construct($jid, $cpid, $seq=0, $lid=0, $eachPointFunc=false, $beginStepFunc=false, $endStepFunc=false, $beginJobFunc=false, $endJobFunc=false) {

		global $xoopsDB,$jobstatus,$stepstatus;

		$this->currentjob=$jid;
		$this->currentplan=$cpid;
		$this->currentseq=$seq;
		if(is_array($lid)) $this->language=$lid;
		else $this->language=array($lid);

		$this->eachPointFunc=$eachPointFunc;
		$this->beginStepFunc=$beginStepFunc;
		$this->endStepFunc=$endStepFunc;
		$this->beginJobFunc=$beginJobFunc;
		$this->endJobFunc=$endJobFunc;

		$currentjob=$this->currentjob;
		$currentplan=$this->currentplan;

		// get job data
		$this->jobdata=array();

		$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_job');
		$sql.=" WHERE job_id = $currentjob ";

		$result = $xoopsDB->query($sql);
		if ($result) {
			if($myrow=$xoopsDB->fetchArray($result)) {
				$this->jobdata=$myrow;
				$this->jobdata['display_job_status']=$jobstatus[$myrow['job_status']];
			}
		}

		// get job step data, just one if a plan was specified, otherise all
		// but skip canceled steps unless explicitly chosen
		$this->jobsteps=array();
		$member_handler =& xoops_gethandler('member');

		$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_job_steps');
		$sql.=" WHERE job = $currentjob ";
		if($currentplan) {
			$sql.=" AND cplan = $currentplan ";
		}
		else {
			$sql.=" AND job_step_status != 'canceled' ";
		}

		$result = $xoopsDB->query($sql);
		$i=0;
		if ($result) {
			while($myrow=$xoopsDB->fetchArray($result)) {
				$this->jobsteps[$i]=$myrow;
				$this->jobsteps[$i]['display_job_step_status']=$stepstatus[$myrow['job_step_status']];

				$this->jobsteps[$i]['assigned_name']=getUserNameFromId($myrow['assigned_uid']);
				$cpointcnt=getCPointCounts($myrow['cplan']);
				$this->jobsteps[$i]['pointcount']=$cpointcnt['count'];
				$this->jobsteps[$i]['lockcount']=$cpointcnt['locks'];
				$this->jobsteps[$i]['tagcount']=$cpointcnt['tags'];
				++$i;
			}
		}
		$this->jobdata['stepcount']=$i;
	}

	/**
	* Perform callback
	* @param mixed $func function to call, array(object,method), function or false
	* @param array $job job data
	* @param mixed $step array of step data or false
	* @param mixed $point array of point data or false
	* @access private
	* @since 1.0.0
	*/
	private function doCallback($func,$job,$step=false,$point=false) {
		if(is_array($func)) {
			$func[0]->{$func[1]}($job,$step,$point);
		} else {
			if($func) $func($job,$step,$point);
		}
	}

	/**
	* Present job data to specified callbacks in sequence:
	*   beginJobFunc - at start of job
	*     beginStepFunc - at start of each step
	*       eachPointFunc - for each control point
	*     endStepFunc - at end of step
	*   endJobFunc - at end of job
	* @access public
	* @since 1.0
	*/
	public function doPrint() {
		global $xoopsDB,$jobstatus,$stepstatus,$seqoptions;

		$currentjob=$this->currentjob;
		$currentseq=$this->currentseq;
		// eventually we will loop through these, but right now grab last one.
		foreach ($this->language as $l) $language=$l;

		$this->jobdata['printed_date_raw']=time();
		$this->jobdata['printed_date']=getDisplayDate($this->jobdata['printed_date_raw']);

		$this->doCallback($this->beginJobFunc,$this->jobdata);

		foreach($this->jobsteps as $jobstepindex=>$jobstepdata) {
		  if(isset($placechain)) unset($placechain);

		  $currentplan=$jobstepdata['cplan'];
		  foreach ($this->language as $language) {

			// get control plan data
			$plandata=array();
			$sql='SELECT * FROM '.$xoopsDB->prefix('gwloto_cplan').', '.$xoopsDB->prefix('gwloto_cplan_detail');
			$sql.=" WHERE cplan_id = cplan and (language_id=$language OR language_id=0)";
			$sql.=" AND cplan_id = $currentplan ";
			$sql.=' ORDER BY language_id ';

			$cnt=0;
			$result = $xoopsDB->query($sql);
			if ($result) {
				while($myrow=$xoopsDB->fetchArray($result)) {
					$plandata=$myrow;
				}
			}

			$currentplace = $plandata['place_id'];

			$sql='SELECT * FROM '.$xoopsDB->prefix('gwloto_place').', '.$xoopsDB->prefix('gwloto_place_detail');
			$sql.=" WHERE place_id=place AND place = $currentplace AND (language_id=$language OR language_id=0)";
			$sql.=' ORDER BY language_id ';

			$result = $xoopsDB->query($sql);
			if ($result) {
				while($myrow=$xoopsDB->fetchArray($result)) {
					$placedata=$myrow;
					unset($placedata['last_changed_by']);
					unset($placedata['last_changed_by']);
				}
			}

			if(!isset($placechain)) {
				$startplace=$currentplace;
				$placechain=array();
				$killcnt=100; // just a safety net

				while($startplace!=0) {

					$sql='SELECT place_id, parent_id FROM '.$xoopsDB->prefix('gwloto_place');
					$sql.=" WHERE place_id=$startplace";

					$result = $xoopsDB->query($sql);
					if ($result) {
						if($myrow=$xoopsDB->fetchArray($result)) {
							$placechain[$myrow['parent_id']]=$myrow['place_id'];
							$startplace=$myrow['parent_id'];
						}
						$xoopsDB->freeRecordSet($result);
					}
					if(--$killcnt<0) break;
				}
			}

			$fullplacename=array();
			if(count($placechain)>0) {
				$i=0;
				while(isset($placechain[$i])) {
					$i=$placechain[$i];
					$fullplacename[]=getPlaceName($i, $language);
				}
			}

			if($plandata['hazard_inventory']=='') $plandata['hazard_inventory']=$placedata['place_hazard_inventory'];
			if($plandata['required_ppe']=='') $plandata['required_ppe']=$placedata['place_required_ppe'];
			foreach($placedata as $i=>$v) $jobstepdata[$i]=$v;
			foreach($plandata as $i=>$v) $jobstepdata[$i]=$v;
			$jobstepdata['fullplacename']=$fullplacename;

			$ml_jobstepdata[$language]=$jobstepdata;
		  } // language loop

		  $this->doCallback($this->beginStepFunc,$this->jobdata,$ml_jobstepdata);

		  // control points
		  $points=array();

		  foreach ($this->language as $language) {

			$orderby=$seqoptions[$currentseq]['sort'];

			$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_cpoint').', '.$xoopsDB->prefix('gwloto_cpoint_detail');
			$sql.= " WHERE cplan_id = $currentplan AND (language_id=$language OR language_id=0) ";
			$sql.= ' AND cpoint = cpoint_id ';
			$sql.= " ORDER BY $orderby, language_id ";
	
			$cnt=0;
			$result = $xoopsDB->query($sql);
			if ($result) {
				while($myrow=$xoopsDB->fetchArray($result)) {
					$i=$myrow['cpoint_id'];
					$points[$i][$language]=$myrow;
					$points[$i][$language]['instructions']=$myrow[$seqoptions[$currentseq]['instructions']];
					$points[$i][$language]['state']=$myrow[$seqoptions[$currentseq]['state']];
				}
			}
		  } // language loop

			foreach($points as $pointdata) {
				$this->doCallback($this->eachPointFunc,$this->jobdata,$ml_jobstepdata,$pointdata);
			}

			$this->doCallback($this->endStepFunc,$this->jobdata,$ml_jobstepdata);

		}
		$this->doCallback($this->endJobFunc,$this->jobdata);
	}

}

?>