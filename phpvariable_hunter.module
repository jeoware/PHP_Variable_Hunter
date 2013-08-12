<?php

/**
 * @file
 * Main file for phpvariable_hunter
 *
 * This module easily finds a variable in a complex tree structure.
 *
 * @defgroup phpvariable_hunter PHP Variable Hunter
 * @ingroup phpvariable_hunter
 */

/**
 *
 * Author and disclaimer information must remain 
 * in this freely distributable source file.
 * 
 *  PHP Variable Hunter - Find buried PHP variables. 
 * For Drupal and any PHP developer.
 * Copyright (c) 2013, Jimmy Olsen, idxSoft  http://www.idxsoft.com.
 * 
 * If this module saves you time or effort, please consider contributing 
 * at http://www.idxsoft.com/contribute
 * 
 * Find variables in complex variable arrays and returns the location.
 * Syntax: Searching for 'Footer' in $form variable.
 * $result = phpvariable_hunter($form, 'Footer', TRUE); // or use pvh();
 * 
 * Matches printed as 
 * ['admin']['nodes']['#options']['37']['title']['data']['#title'] 
 * value ==> This is the Footer
 * 
 * Also returns the results in a variable array for debugging.
 * The search is limited to 15 tree levels to ensure proper performance.
 * 
 * This program is free software; you can redistribute it and/or modify it 
 * under the terms of the GNU General Public
 *  License as published by the Free Software Foundation version 2
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY;  
 * without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  
 * See the GNU General Public License for more details.
 */
$callerinfo = '';

/**
 * Function  pvh - Shorthand for phpvariable_hunter.
 */
function pvh($array, $needle, $exact_match = FALSE) {
  phpvariable_hunter_getcaller(1);
  $ret = phpvariable_hunter($array, $needle, $exact_match);
  return $ret;
}

/**
 * Retrieve the called procedure variable from the stack.
 */
function phpvariable_hunter_getcaller($depth) {
  $trace = debug_backtrace();
  $ret = '';
  if (isset($trace[$depth]['file'])) {
    $lines = file($trace[$depth]['file']);
    $line = $lines[$trace[$depth]['line'] - 1];
    $expr = $line;
    $expr = preg_replace('/\s*dump\(/i', '', $expr);
    $expr = preg_replace('/\);\s*/', '', $expr);
    // Parse this down to the first parameter.
    $pos = strpos($expr, '(');
    if ($pos > 0) {
      $pos1 = strpos($expr, ',', $pos + 1);
      if ($pos1 > 0) {
        $ret = trim(substr($expr, $pos + 1, $pos1 - $pos - 1));
        $ret = substr($ret, 1, strlen($ret) - 1);
        $GLOBALS['phpvariable_hunter_caller'] = $ret;
      }
    }
  }
}

/**
 * Function phpvariable_hunter - performs a nested variable lookup.
 */
function phpvariable_hunter($array, $needle, $exact_match = FALSE) {
  if (!isset($GLOBALS['phpvariable_hunter_caller'])) {
    phpvariable_hunter_getcaller(1);
  }
  $GLOBALS['phpvariable_hunter_stack'] = array();
  $GLOBALS['phpvariable_hunter_types'] = array();
  $GLOBALS['phpvariable_hunter_results'] = array();
  $ret = phpvariable_hunter_internalcall($array, $needle, $exact_match);
  unset($GLOBALS['phpvariable_hunter_stack']);
  unset($GLOBALS['phpvariable_hunter_types']);
  // Now return the results...... both to variable and screen.
  $ret = $GLOBALS['phpvariable_hunter_results'];
  unset($GLOBALS['phpvariable_hunter_results']);
  // A header to find what we searched for.
  print ('</br></br></br>');
  print (t('PHP Variable Hunter results for needle = ') . $needle . '</br></br>');
  if (count($ret) == 0) {
    // Return -- nothing found.
    print (t('No results found.'));
    print ('</br>');
  }
  else {
    // Return all the variables.
    foreach ($ret as $msg) {
      print ($msg . '</br>');
    }
    print ('</br>' . count($ret) . t(' Matches found') . '</br>');
  }
  return $ret;
}

/**
 * Nested procedure - it calls itself untill the depth of the variable tree.
 */
function phpvariable_hunter_internalcall($array, $needle, $exact_match) {
  try {
    $ret = array();
    if ((is_array($array)) || (is_object($array))) {
      foreach ($array as $key => $val) {
        if ((isset($val) && !is_array($val)) && !is_object($val)) {
          // Here's where we check to see if we have a match.
          $ok = FALSE;
          if (!$exact_match) {
            // Looking for the value in any part of the variable.
            if (is_string($val)) {
              $pos = strpos($val, $needle);
              if ($pos !== FALSE) {
                // Found it.
                $ok = TRUE;
              }
            }
            else {
              if (is_numeric($val)) {
                if ($needle == strval($val)) {
                  // Found it.
                  $ok = TRUE;
                }
              }
            }
          }
          else {
            // Looking for exact match here -- simple compare.
            if ($needle === $val) {
              $ok = TRUE;
            }
          }
          if ($ok) {
            // Quick check if overflow.
            foreach ($GLOBALS['phpvariable_hunter_types'] as $var) {
              if ($var == 'overflow') {
                $ok = FALSE;
              }
            }
          }
          if ($ok) {
            $answer = '$' . $GLOBALS['phpvariable_hunter_caller'];
            $wasobject = FALSE;
            foreach ($GLOBALS['phpvariable_hunter_stack'] as $key_stack => $var) {
              // Have to determine if the prior entry was a class.
              if ($GLOBALS['phpvariable_hunter_types'][$key_stack + 1] == 'object') {
                $answer .= '[\'' . $var . '\']->';
                $wasobject = TRUE;
              }
              else {
                if ($wasobject) {
                  $answer .= $var;
                  $wasobject = FALSE;
                }
                else {
                  $answer .= '[\'' . $var . '\']';
                }
              }
            }
            // Add the current level to the answer - it's not there.
            if ($wasobject) {
              $answer .= $key;
            }
            else {
              $answer .= '[\'' . $key . '\']';
            }
            if ($answer != '') {
              $GLOBALS['phpvariable_hunter_results'][] = $answer . ';  ==  ' . $val;
            }
          }
        }
        else {
          if (count($GLOBALS['phpvariable_hunter_stack']) < 15) {
            // Save the key before tracing down.
            $GLOBALS['phpvariable_hunter_stack'][count($GLOBALS['phpvariable_hunter_stack'])] = $key;
            $GLOBALS['phpvariable_hunter_types'][count($GLOBALS['phpvariable_hunter_stack'])] = gettype($val);
            phpvariable_hunter_internalcall($val, $needle, $exact_match);
            unset($GLOBALS['phpvariable_hunter_stack'][count($GLOBALS['phpvariable_hunter_stack']) - 1]);
            unset($GLOBALS['phpvariable_hunter_types'][count($GLOBALS['phpvariable_hunter_stack']) - 1]);
          }
          else {
            // Depth greater than search limitation, continue with the loop.
            $GLOBALS['phpvariable_hunter_types'][count($GLOBALS['phpvariable_hunter_stack'])] = 'overflow';
            continue;
          }
        }
      }
      return $ret;
    }
  }
  catch (Exception $e) {
    unset($GLOBALS['phpvariable_hunter_stack'][count($GLOBALS['phpvariable_hunter_stack']) - 1]);
    unset($GLOBALS['phpvariable_hunter_types'][count($GLOBALS['phpvariable_hunter_stack']) - 1]);
  }
}
