<?php
/**
* sortpoint.php - reorder control points within a plan
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

include '../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_sortpoint.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');

include ('include/seqoptions.php');

$selectalert=_MD_GWLOTO_SORTPOINT_SELECT;
$sortelement='pointsort';
$sort_js = <<<ENDJSCODE
function move(f,bDir) {
  var el = f.elements["$sortelement"]
  var idx = el.selectedIndex
  if (idx==-1) 
    alert("$selectalert")
  else {
    var nxidx = idx+( bDir? -1 : 1)
    if (nxidx<0) return; // nxidx=el.length-1
    if (nxidx>=el.length) return; // nxidx=0
    var oldVal = el[idx].value
    var oldText = el[idx].text
    el[idx].value = el[nxidx].value
    el[idx].text = el[nxidx].text
    el[nxidx].value = oldVal
    el[nxidx].text = oldText
    el.selectedIndex = nxidx
  }
}

function reverseorder(f) {
  var el = f.elements["$sortelement"];
  var b = 0;
  var t = el.length;
  t = t-1;
  while (b<t) {
    var oldVal = el[t].value;
    var oldText = el[t].text;
    el[t].value = el[b].value;
    el[t].text = el[b].text;
    el[b].value = oldVal;
    el[b].text = oldText;
    b = b+1;
    t = t-1;
  }
}

function processForm(f) {
  for (var i=0;i<f.length;i++) {	
    var el = f[i]
    // If reorder listbox, then generate value for hidden field
    if (el.name=="$sortelement") {
      var strIDs = ""
      for (var j=0;j<f[i].options.length;j++)
        strIDs += f[i].options[j].value + ","
        f.elements['neworder'].value = strIDs.substring(0,strIDs.length-1)
    }
  }
}
ENDJSCODE;

$xoTheme->addScript( null, array( 'type' => 'text/javascript' ), $sort_js );

// leave if we don't have any control plan authority
if(!(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_CP_VIEW]) ||
isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS]))) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

$show_edit=false;
if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || 
isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
$show_edit=true;
}

$op='display';
if(isset($_POST['submit'])) {
	$op='update';
}

$cplan_id=$currentplan;

$cpoints=getControlPoints($cplan_id, $language, $seqoptions[$currentseq]['sort']);

// leave if there is nothing to sort
if(count($cpoints)<2) {
	redirect_header("editplan.php?cpid=$currentplan", 3, _MD_GWLOTO_SORTPOINT_EMPTY);
}

if($op=='update') {
	if(isset($_POST['neworder'])) {
		$neworder=array();
		$neworder=explode(',',$_POST['neworder']);
	}
	else $op='display';
}

if($op=='update') {
	foreach ($neworder as $i => $point) {
		if(isset($cpoints[$point])) {
			$cpoints[$point][$seqoptions[$currentseq]['sort']] = $i;
		}
		else $op='display';
	}
}

if($op=='update') {
	foreach ($cpoints as $i => $v) {
		$sql ='UPDATE '.$xoopsDB->prefix('gwloto_cpoint');
		$sql.=' SET seq_disconnect = '.$v['seq_disconnect'];
		$sql.=' , seq_reconnect = '.$v['seq_reconnect'];
		$sql.=' , seq_inspection = '.$v['seq_inspection'];
		$sql.=' WHERE cpoint_id = '. $v['cpoint_id']. ' ';
		$result = $xoopsDB->queryF($sql);
		}
	unset($cpoints);
	$cpoints=getControlPoints($cplan_id, $language, $seqoptions[$currentseq]['sort']);
	$op='display';
}

$token=0;

$caption = sprintf(_MD_GWLOTO_SORTPOINT_FORM, getCplanName($currentplan, $language));
$form = new XoopsThemeForm($caption, 'form1', '', 'POST', $token);

$caption = _MD_GWLOTO_SORTPOINT_ACTIONS;
$buttontray=new XoopsFormElementTray($caption, '');

$button_moveup=new XoopsFormButton('', 'moveup', _MD_GWLOTO_SORTPOINT_UP, 'button');
$button_moveup->setExtra('onClick="move(this.form,true)" ');
$buttontray->addElement($button_moveup);

$button_movedown=new XoopsFormButton('', 'movedown', _MD_GWLOTO_SORTPOINT_DOWN, 'button');
$button_movedown->setExtra('onClick="move(this.form,false)" ');
$buttontray->addElement($button_movedown);

$button_reverse=new XoopsFormButton('', 'reverse', _MD_GWLOTO_SORTPOINT_REVERSE, 'button');
$button_reverse->setExtra('onClick="reverseorder(this.form)" ');
$buttontray->addElement($button_reverse);

$button_submit=new XoopsFormButton('', 'submit', _MD_GWLOTO_SORTPOINT_SAVE, 'submit');
$button_submit->setExtra('onClick="processForm(this.form)" ');
$buttontray->addElement($button_submit);

$form->addElement($buttontray);

// XoopsFormSelect( string $caption, string $name, [mixed $value = null], [int $size = 1], [bool $multiple = false])
$listbox = new XoopsFormSelect(_MD_GWLOTO_SORTPOINT_CPOINTS, 'pointsort', null, count($cpoints), false);
foreach ($cpoints as $i => $v) {
	$listbox->addOption($i, $v['cpoint_name'].' - '.$v[$seqoptions[$currentseq]['state']]);
}
$form->addElement($listbox);

$form->addElement($buttontray);

if(count($seqoptions)>1) {
	$caption = _MD_GWLOTO_SORTPOINT_SEQ;
	$radio=new XoopsFormRadio($caption, 'seq', $currentseq, '');

	foreach($seqoptions as $i => $v) {
		$radio->addOption($i, $v['label']);
	}
//	$radio->setExtra('onChange="document.pointsort.submit()" ');
	$form->addElement($radio);

	$form->addElement(new XoopsFormButton('', 'seqchange', _MD_GWLOTO_SORTPOINT_SEQ_SHOW, 'submit'));
}

$form->addElement(new XoopsFormHidden('cpid', $cplan_id));
$form->addElement(new XoopsFormHidden('neworder', ''));
$body=$form->render();


//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//if(isset($neworder)) $debug.='<pre>$neworder='.print_r($neworder,true).'</pre>';
//$debug.='<pre>$cpoints='.print_r($cpoints,true).'</pre>';

setPageTitle(_MD_GWLOTO_TITLE_SORTPOINT);

if(isset($body)) $xoopsTpl->assign('body', $body);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>