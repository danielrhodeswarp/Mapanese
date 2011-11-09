<?php

/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//Make an array suitable for makeSelectFromArray() by getting values from the DB
function makeArrayForSelect($connection, $tableName, $valueField, $textField, $orderBy = null, $textFieldValueToIgnore = null)
{
	$returnArray = array();
	
	$query = "SELECT {$valueField}, {$textField} FROM {$tableName}";
	
	if(!is_null($orderBy))
	{
		$query .= " ORDER BY {$orderBy}";
	}
	
	$results = $connection->query($query);
	
	//Get each row of data on each iteration until there are no more rows
	while($row = $results->fetchRow())
	{
		if(!is_null($textFieldValueToIgnore) and strval($row[$textField]) === strval($textFieldValueToIgnore))
		{
			continue;
		}
		
		$returnArray[$row[$valueField]] = $row[$textField];
	}
	
	return $returnArray;
}

//Return a <select> string based on passed hash array
function makeSelectFromArray($selectName, $array, $emptyFieldText = null, $selectedValue = null)
{
	$returnString = "<select name='{$selectName}' id='{$selectName}'>";
	
	//Insert an empty value if we have $emptyFieldText (always value='')
	if(!is_null($emptyFieldText))
	{
		$returnString .= "<option value=''>{$emptyFieldText}</option>";
	}
	
	//Loop through the array
	foreach($array as $value => $text)
	{
		$selected = '';
		
		if(!is_null($selectedValue) and strval($value) === strval($selectedValue))
		{
			$selected = " selected='selected'";
		}
		
		$returnString .= "<option value='{$value}'{$selected}>{$text}</option>";
	}
	
	$returnString .= '</select>';
	
	return $returnString;
}

//Return a <select> string based on the two passed hash arrays
//(with madatory <optgroup>s)
function makeSelectFromTwoArrays($selectName, $array1, $optgrouplabel1, $array2, $optgrouplabel2, $emptyFieldText = null, $selectedValue = null)
{
	$returnString = "<select name='{$selectName}' id='{$selectName}'>";
	
	//Insert an empty value if we have $emptyFieldText (always value='')
	if(!is_null($emptyFieldText))
	{
		$returnString .= "<option value=''>{$emptyFieldText}</option>";
	}
	
	$returnString .= "<optgroup label='{$optgrouplabel1}'>";
	
	//Loop through the first array
	foreach($array1 as $value => $text)
	{
		$selected = '';
		
		if(!is_null($selectedValue) and strval($value) === strval($selectedValue))
		{
			$selected = " selected='selected'";
		}
		
		$returnString .= "<option value='{$value}'{$selected}>{$text}</option>";
	}
	
	$returnString .= "</optgroup><optgroup label='{$optgrouplabel2}'>";
	
	//Loop through the second array
	foreach($array2 as $value => $text)
	{
		$selected = '';
		
		if(!is_null($selectedValue) and strval($value) === strval($selectedValue))
		{
			$selected = " selected='selected'";
		}
		
		$returnString .= "<option value='{$value}'{$selected}>{$text}</option>";
	}
	
	$returnString .= '</optgroup></select>';
	
	return $returnString;
}

//Return a <select> string based on database details and data
//THIS FUNCTION COMBINES makeArrayForSelect() AND makeSelectFromArray()!
function makeSelectFromDatabase($selectName, $connection, $tableName, $valueField, $textField, $emptyFieldText = null, $selectedValue = null, $orderBy = null)
{
	$returnString = "<select name='{$selectName}' id='{$selectName}'>";
	
	//Insert an empty value if we have $emptyFieldText (always value='')
	if(!is_null($emptyFieldText))
	{
		$returnString .= "<option value=''>{$emptyFieldText}</option>";
	}
	
	$query = "SELECT {$valueField}, {$textField} FROM {$tableName}";
	
	if(!is_null($orderBy))
	{
		$query .= " ORDER BY {$orderBy}";
	}
	
	$results = $connection->query($query);
	
	//Get each row of data on each iteration until there are no more rows
	while($row = $results->fetchRow())
	{
		$selected = '';
		
		if(!is_null($selectedValue) and strval($row[$valueField]) === strval($selectedValue))
		{
			$selected = " selected='selected'";
		}
		
		$returnString .= "<option value='{$row[$valueField]}'{$selected}>{$row[$textField]}</option>";
	}
	
	$returnString .= '</select>';
	
	return $returnString;
}

//Return a <select> string based on database details and data
function makeSelectWithOptgroupFromDatabase($selectName, $connection, $tableName, $valueField, $textField, $labelField, $emptyFieldText = null, $selectedValue = null, $orderBy = null)
{
	$returnString = "<select name='{$selectName}' id='{$selectName}'>";
	
	//Insert an empty value if we have $emptyFieldText (always value='')
	if(!is_null($emptyFieldText))
	{
		$returnString .= "<option value=''>{$emptyFieldText}</option>";
	}
	
	$query = "SELECT {$valueField}, {$textField}, {$labelField} FROM {$tableName}";
	
	if(!is_null($orderBy))
	{
		$query .= " ORDER BY {$orderBy}";
	}
	
	$results = $connection->query($query);
	
	$label = 'sdfkjasdfkjahsdfkjashdfkljasdhf';
	$firstTime = true;
	
	//Get each row of data on each iteration until there are no more rows
	while($row = $results->fetchRow())
	{
		if($row[$labelField] != $label)
		{
			$label = $row[$labelField];
			
			if(!$firstTime)
			{
				$returnString .= '</optgroup>';
			}
			
			$returnString .= "<optgroup label='{$row[$labelField]}'>";
			
			$firstTime = false;
		}
		
		$selected = '';
		
		if(!is_null($selectedValue) and strval($row[$valueField]) === strval($selectedValue))
		{
			$selected = " selected='selected'";
		}
		
		$returnString .= "<option value='{$row[$valueField]}'{$selected}>{$row[$textField]}</option>";
	}
	
	$returnString .= '</optgroup></select>';
	
	return $returnString;
}

//Make an INSERT clause from a hash
function makeInsertSqlFromArray($tableName, $array)
{
	$returnString = "INSERT INTO {$tableName}(";
	
	foreach($array as $key => $value)
	{
		$returnString .= $key . ',';
	}
	
	$returnString = rtrim($returnString, ',');
	
	$returnString .= ') VALUES(';
	
	foreach($array as $key => $value)
	{
		$returnString .= "'" . addslashes($value) . "',";
	}
	
	$returnString = rtrim($returnString, ',');
	
	$returnString .= ')';
	
	return $returnString;
}

//Make an UPDATE clause from a hash
function makeUpdateSqlFromArray($tableName, $array, $where)
{
	$returnString = "UPDATE {$tableName} SET ";
	
	foreach($array as $key => $value)
	{
		$returnString .= "{$key} = '" . addslashes($value) . "',";
	}
	
	$returnString = rtrim($returnString, ',');
	
	$returnString .= " WHERE {$where}";
		
	return $returnString;
}

//Make a SELECT clause from a hash
function makeSelectSqlFromArray($tableName, $array)
{
	$returnString = "SELECT * FROM {$tableName}";
	
	$conds = array();
	
	foreach($array as $field => $subArray)
	{
		list($type, $value) = $subArray;
		
		$value = addslashes($value);
		
		if(!empty($value))
		{
			switch($type)
			{
				case 'exact':
					$conds[] = "{$field} = '{$value}'";
					break;
					
				case 'about':
					$conds[] = "{$field} LIKE '%{$value}%'";
					break;
				
				case 'greater_than_or_equal':
					$conds[] = "{$field} >= '{$value}'";
					break;
			}
		}
	}
	
	if(!empty($conds))
	{
		$returnString .= ' WHERE ';
	}
	
	$returnString .= join(' AND ', $conds);
	
	return $returnString;
}