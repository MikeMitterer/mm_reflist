<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Mike Mitterer (office@bitcon.at)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Reference-List' for the 'mm_reflist' extension.
 *
 * @author	Mike Mitterer <office@bitcon.at>
 */


require_once(t3lib_extMgm::extPath('mm_bccmsbase').'lib/class.mmlib_extfrontend.php');

//class tx_mmreflist_pi1 extends tslib_pibase {
class tx_mmreflist_pi1 extends mmlib_extfrontend {
	var $prefixId 			= 'tx_mmreflist_pi1';		// Same as class name
	var $scriptRelPath 	= 'pi1/class.tx_mmreflist_pi1.php';	// Path to this script relative to the extension dir.
	var $pi_checkCHash 	= TRUE;
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	
		{
		$aInitData['tablename'] 		= 'tx_mmreflist_projects';
		$aInitData['uploadfolder'] 	= 'tx_mmreflist';
		$aInitData['extensionkey'] 	= 'mm_reflist';
		$this->initFromArray($conf,$aInitData);

		$content = '';
		switch((string)$conf['CMD'])	
			{
			case 'singleView':
				list($t) = explode(':',$this->cObj->currentRecord);
				$this->internal['currentTable']	=	$t;
				$this->internal['currentRow']		=	$this->cObj->data;
				$content = $this->singleView($content);
			break;
			default:
				$content = $this->listView($content);
			break;
			}
			
		return $this->pi_wrapInBaseClass($content);
		}
	
	/**
	 * [Put your description here]
	 */
	function listView($content)	{
		$this->pi_setPiVarDefaults();
		
		$lConf = $this->conf['listView.'];	// Local settings for the listView function
	
		if ($this->piVars['showUid'])	
			{	// If a single element should be displayed:
			$this->internal['currentTable'] = 'tx_mmreflist_projects';
			$this->internal['currentRow'] = $this->pi_getRecord('tx_mmreflist_projects',$this->piVars['showUid']);
	
			$content = $this->singleView($content);
			return $content;
			} 
			
		$items=array(
			'1'=> $this->pi_getLL('list_mode_1','Mode 1'),
			'2'=> $this->pi_getLL('list_mode_2','Mode 2'),
			'3'=> $this->pi_getLL('list_mode_3','Mode 3'),
			);
			
		if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
		if (!isset($this->piVars['mode']))	$this->piVars['mode']=1;

			// Initializing the query parameters:
		list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
		$this->internal['results_at_a_time']=t3lib_div::intInRange($lConf['results_at_a_time'],0,1000,3);		// Number of results to show in a listing.
		$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,2);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
		$this->internal['searchFieldList']='name,shorttext,description,phone,fax,email,web';
		$this->internal['orderByList']='uid,name,phone,fax,email,web';

		// Wenn für eine Seite ein Filter gesetzt ist - dann hier einbauen
		$strWhereStatement = '';
		//debug($this->conf['filter.']);
		if(is_array($this->conf['filter.']))
			{
			foreach($this->conf['filter.'] as $key=>$value)
				{ 
				if($key == 'calendarweek' && $value == 'true') $value = date("W");
				
				$strWhereStatement .= "AND $key = '$value'";
				//debug($strWhereStatement) ;
				}
			}
		if(is_array($this->piVars["search"]))
			{
			foreach($this->piVars["search"] as $key=>$value)
				{
				if($value == -1) continue;
				$strWhereStatement .= "AND $key = '$value'";
				}
			}

			// Get number of records:
		$res = $this->pi_exec_query('tx_mmreflist_projects',1,$strWhereStatement);
		list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

			// Make listing query, pass query to SQL database:
		$res = $this->pi_exec_query('tx_mmreflist_projects',$strWhereStatement);
		$this->internal['currentTable'] = 'tx_mmreflist_projects';


			// Put the whole list together:
		$fullTable='';	// Clear var;
	#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!

			// Adds the mode selector.
		if($lConf['showModeSelector'] == 1) $fullTable .= $this->pi_list_modeSelector($items);

			// Adds the whole list table
			// Bei der erzeugeten Tabelle wird auch der Klassenname angehängt
		$fullTable .= $this->pi_list_makelist($res,'border="1" cellspacing="0" cellpadding="0"' . $this->pi_classParam($strTableClassName));

			// Adds the search box:
		if($lConf['showSearchBox'] == 1) $fullTable .= $this->pi_list_searchBox();

			// Adds the result browser:
		if($lConf['showBrowserResults'] == 1) $fullTable .= $this->pi_list_browseresults();	

			// Returns the content from the plugin.
		return $fullTable;
	}
	/**
	 * [Put your description here]
	 */
	function singleView($content)	{
		$this->pi_setPiVarDefaults();
		
	 	$lConf = $this->conf['singleView.'];	// Local settings for the singleView function
		$aGETVars 			= t3lib_div::_GET();	// Commandline
		$aPOSTVars 			= t3lib_div::_POST(); 	// Form
		$objUserAuth 		= t3lib_div::makeInstance('tslib_feUserAuth');
	 	
			// This sets the title of the page for use in indexed search results:
		if ($this->internal['currentRow']['title'])	$GLOBALS['TSFE']->indexedDocTitle=$this->internal['currentRow']['title'];
	
		$strContent = '';
		$template 			= $this->_getTemplate('single_view.tmpl');	
		$templateListCol 	= $this->cObj->getSubpart($template,'###LIST_COL###');
		$templateMarker 	= $this->cObj->getSubpart($template,'###MARKERLINE###');

		$markerArray['###SYS_UID###'] 					= $this->internal["currentRow"]["uid"];
		$markerArray['###SYS_CURRENTTABLE###'] 	= $this->internal["currentTable"];
		$markerArray['###SYS_LASTUPDATE###'] 		= date("d-m-Y H:i",$this->internal["currentRow"]["tstamp"]);
		$markerArray['###SYS_CREATION###'] 			= date("d-m-Y H:i",$this->internal["currentRow"]["crdate"]);
		$markerArray['###SYS_BACKLINK###'] 			= $this->pi_list_linkSingle($this->pi_getLL("back","Back"),0);
		$markerArray['###SYS_EDITPANEL###'] 		= $this->pi_getEditPanel();
		$markerArray['###SYS_ALLFIELDS###']			= '';

		// Reihenfolge der Felder festlegen
		$aFieldsToDisplay = strlen($lConf['displayOrder']) > 0 ? explode(',',$lConf['displayOrder']) : array_keys($this->internal["currentRow"]);

		// Diese Felder werden ausgeschlossen wenn sie leer oder auf 0 sind
		$aHideIfEmpty = strlen($lConf['hideIfEmpty']) > 0 ? explode(',',$lConf['hideIfEmpty']) : array();
		foreach($aHideIfEmpty as $evalue) $aTemp[] = trim($evalue);
		$aHideIfEmpty = $aTemp;
		
		$aFieldsToDisplay = array_keys($this->internal["currentRow"]);
		if(isset($lConf['displayOrder'])) $aFieldsToDisplay = split(',',$lConf['displayOrder']);
		
		$nCounter = 0;	
		$strColContent = '';
		foreach($aFieldsToDisplay as $key)
			{
			$key = trim($key);
				
			// Wenn im KEY (also im Feldnamen eine [ vorkommt dann ist das eine Leerzeile	
			if(preg_match('#^\[marker(.*)\]$#',$key,$aMatches)) {
				$markerMarker['###MARKERTEXT###'] = '&nbsp;';
				if(isset($aMatches[1]) && trim(strlen($aMatches[1])>0)) 
					{
					$strMarkerLable = trim($aMatches[1]);
					$markerMarker['###MARKERTEXT###'] = $this->pi_getLL($strMarkerLable,$strMarkerLable);
					}
				$strColContent .= $this->cObj->substituteMarkerArray($templateMarker,$markerMarker);
				continue;
				}
			
			$strFieldHeader = $this->getFieldHeader($key);
		
			// Wenn am Anfang und am Ende des Feldnamens ein [ bzw. ] steht dann ist das normalerweise der interne Name (internes Feld)
			if(preg_match('#^\[.*\]$#',$strFieldHeader)) continue;
			
			// Wenn leer und wenn der Status des Feldes auf ausblenden wenn leer
			if(($this->internal["currentRow"][$key] === '' || 
				$this->internal["currentRow"][$key] === 0) && 
				in_array($key,$aHideIfEmpty,true))
				{ 
				//debug("$key ->" . $this->getSingleViewFieldContent($key) . '#' . $this->internal["currentRow"][$key] . '#');
				continue; 
				}

			$markerArray['###SYS_ALLFIELDS###'] .= $key . ', ';
			// Die beiden Felder werden auf den selben Wert gezogen da damit 
			// entweder eine Tabelle erstellt werden kann die immer die Selben Zeilen verwendet
			// sowie eine Tabelle die ein individuelles Layout hat
			$markerArrayCol['###LABLE###']	= '<div'.$this->pi_classParam('lable ' . 'lable_' . $key).'>' . 
				$strFieldHeader . '</div>';

			$markerArrayCol['###LABLE_' . strtoupper($key) . '###'] = $markerArrayCol['###TITLE###'];
				
			// Und hier kommen die Feldwerte
			$markerArrayCol['###FIELD###']	= '<div'.$this->pi_classParam('field ' . 'field_' . $key).'>' . 
				$this->getSingleViewFieldContent($key) . '</div>';

			$markerArrayCol['###FIELD_' .  strtoupper($key) . '###']	= $markerArrayCol['###FIELD###'];
			
			$markerArrayCol['###COLCLASS###'] = ($nCounter % 2 ? $this->pi_classParam("listcol-odd") : "");
			
			$strColContent .= $this->cObj->substituteMarkerArray($templateListCol,$markerArrayCol);
			$nCounter++;
			}
		
		if($lConf['showFieldNames']	== 0) $markerArray['###SYS_ALLFIELDS###'] = '';
		
		//Löschen des Markerblocks - sonst wird dieser am Ende noch 1x angezeigt
		$template = $this->cObj->substituteSubpart($template,'###MARKERLINE###','');
		
		// Contents der Spalten wird in den Platzhalter LIST_COL geschrieben
		$template = $this->cObj->substituteSubpart($template,'###LIST_COL###',$strColContent);
		
		// Arraykeys von markerArray ersetzen die jeweiligen Platzhalter in $template
		$template = $this->cObj->substituteMarkerArray($template,$markerArray);
		
		$strContent = $this->cObj->substituteMarkerArray($template,$markerArrayCol);
		
		$strContent .= $this->pi_getEditPanel();
	
		return $strContent;
	}
	/**
	 * [Put your description here]
	 */
	function pi_list_row($c)	{
		$strTemplateName	= ($this->conf["templateFile"] ? $this->conf["templateFile"] : 'list_view.tmpl');		
		$editPanel 				= $this->pi_getEditPanel();
		
		if ($editPanel)	$editPanel="<TD>".$editPanel."</TD>";
	
		foreach($this->internal["currentRow"] as $key=>$value)
			{
			$markerArray['###' .  strtoupper($key) . '_CLASS###']	= $this->pi_classParam($key);
			
			$markerArray['###' .  strtoupper($key) . '###']	= '<span'.$this->pi_classParam($key).'>' . 
				$this->getFieldContent($key) . '</span>';
			}
			
		$markerArray['###ROWCLASS###'] 			= ($c % 2 ? $this->pi_classParam("listrow-odd") : $this->pi_classParam("listrow-even"));
		$markerArray['###ROWCLASS2###'] 		= ($c % 2 ? $this->pi_classParam("listrow2-odd") : $this->pi_classParam("listrow2-even"));

		$markerArray['###SUBTABLE1CLASS###'] 	= $this->pi_classParam("subtable1");
		$markerArray['###SUBTABLE2CLASS###'] 	= $this->pi_classParam("subtable2");
		$markerArray['###SUBTABLE3CLASS###'] 	= $this->pi_classParam("subtable3");
		
		$markerArray['###FOOTERCLASS###']			= $this->pi_classParam('listView-footer');
		
		$markerArray['###EDITPANEL###'] 			= $editPanel;


		//---------------------------------
		$template 				= $this->_getTemplate($strTemplateName);
		$templateFieldRow = $this->cObj->getSubpart($template,'###LIST_ROW###');
		
		return $this->cObj->substituteMarkerArray($templateFieldRow,$markerArray);
	}
	
function pi_list_header()	{
		$lConf 						= $this->conf["listView."];
		$aFields 					= $GLOBALS['TYPO3_DB']->admin_get_fields($this->tablename);
		$strTemplateName	= ($this->conf["templateFile"] ? $this->conf["templateFile"] : 'list_view.tmpl');		

		// Header soll nicht angezeigt werden
		if(isset($lConf['showHeader']) && $lConf['showHeader'] == 0) return '';
			
		foreach($aFields as $key=>$value)
			{
			$markerArray['###HEADER_' .  strtoupper($key) . '###']	= '<div'.$this->pi_classParam('header_' . $key).'>' . 
				$this->getFieldHeader($key) . '</div>';			
			}
		$markerArray['###HEADERCLASS###'] = $this->pi_classParam("listheader");

		$template = $this->_getTemplate($strTemplateName);
		$templateHeader = $this->cObj->getSubpart($template,'###LIST_HEADER###');
		
		$strOutput = $this->cObj->substituteMarkerArray($templateHeader,$markerArray);
		//debug($strOutput);
		
		return $strOutput;
		}
		
	/**
	 * [Put your description here]
	 */
	function getFieldContent($fieldname)	{
		switch($fieldname) {
			case 'uid':
				return $this->pi_list_linkSingle($this->internal['currentRow'][$fieldname],$this->internal['currentRow']['uid'],1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
			break;
			
			case 'groupname':
				return $this->getRelationData($fieldname,'tx_mmreflist_groups'); 
				break;
			
			default:
				return $this->_getAutoFieldContent($fieldname,$this->internal['currentRow'][$fieldname]);
			break;
		}
	}
	
	function getSingleViewFieldContent($fieldName)	{
		switch($fieldName)
			{
			case "title": 
				return $this->internal["currentRow"][$fieldName];
				break;
			
			default:
				return $this->getFieldContent($fieldName);
				break;
			}
		}
	
	/**
	 * [Put your description here]
	 */
	function getFieldHeader($fieldname)	{
		switch($fieldname) {
			
			default:
				return $this->pi_getLL('listFieldHeader_' . $fieldname,'[' . $fieldname . ']');
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldHeader_sortLink($fieldname)	
		{
		return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fieldname),array('sort'=>$fieldname.':'.($this->internal['descFlag']?0:1)));
		}
	
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_reflist/pi1/class.tx_mmreflist_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_reflist/pi1/class.tx_mmreflist_pi1.php']);
}

?>