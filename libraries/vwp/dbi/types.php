<?php

/**
 * Virtual Web Platform - DBI Types
 *  
 * This file provides the database types
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License     
 */

VWP::RequireLibrary('vwp.dbi.types.singlefieldidentity');
VWP::RequireLibrary('vwp.dbi.types.multifieldidentity');

// Query Types

VWP::RequireLibrary('vwp.dbi.types.query.datasources');
VWP::RequireLibrary('vwp.dbi.types.query.fieldlist');
VWP::RequireLibrary('vwp.dbi.types.query.filter');
VWP::RequireLibrary('vwp.dbi.types.query.filterlist');
VWP::RequireLibrary('vwp.dbi.types.query.relationshipgrouplist');
VWP::RequireLibrary('vwp.dbi.types.query.summaryoptions');
VWP::RequireLibrary('vwp.dbi.types.query.tables');
VWP::RequireLibrary('vwp.dbi.types.query.valuelist');
VWP::RequireLibrary('vwp.dbi.types.query.labellist');
