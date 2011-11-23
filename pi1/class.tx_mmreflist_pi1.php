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
	 * Main-Function - handles the different Views
	 */
	function main($content,$conf)	
		{
		//debug($GLOBALS);

		$aInitData['tablename'] 	= 'tx_mmreflist_projects';
		$aInitData['uploadfolder'] 	= 'tx_mmreflist';
		$aInitData['extensionkey'] 	= 'mm_reflist';
		$this->initFromArray($conf,$aInitData);

		$content = $this->getContentForView();
			
		return $this->pi_wrapInBaseClass($content);
		}
		
	/**
	 * Basefunction for generating the conten for a specific field from
	 * the current recordset (or dummy-field)
	 *
	 * In this case the function is overwritten because of the field 'groupname'
	 * This field has a MM-Relation to the table tx_mmreflist_groups.
	 * But it is still very easy to get the right data from the MM-Table
	 *
	 * @param	[string]		$fieldname: Name of the table field
	 * @return	[string]	Processed content of the field
	 */
	function getFieldContent($fieldname)	
		{	
		switch($fieldname)
			{
			case 'groupname':
				$uid = $this->internal['currentRow']['uid'];
				$content = $this->getDataFromForeignTable($uid,'tx_mmreflist_groups','groupname');
				if(is_array($content)) $content = implode(', ',$content);
				
				return $this->getAutoFieldContent($fieldname,$content);
				break;
					
			default:
				return mmlib_extfrontend::getFieldContent($fieldname);
			break;
			}
		}

	}
	
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_reflist/pi1/class.tx_mmreflist_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_reflist/pi1/class.tx_mmreflist_pi1.php']);
}

?>