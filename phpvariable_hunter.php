<?php

/**
 *
 * Author and disclaimer information must remain 
 * in this freely distributable source file.
 * 
 * PHP Variable Hunter - Find buried PHP variables. 
 * Copyright (c) 2013, Jimmy Olsen, idxSoft  http://www.idxsoft.com.
 * v0.83 - 8/27/2013
 * Lastest version and Drupal (.module) available at
 * http://www.idxsoft.com/phpvariablehunter
 * 
 * Finds variables in complex variable arrays and returns the references.
 * 
 * Syntax: $array_references = pvh($input_array, 'needle', EXACT_MATCH);
 *
 * For example, $results = pvh($form, 'footer');
 * Searches through the array $form for case-insensitive text: 'footer'.
 * 
 * Matches are printed to the screen in PHP variable format as follows:
 * $form['admin']['nodes']['#options']['37']['title']->data; == admin/footer
 * 
 * Also returns results in a variable array so debugger can stay in session.
 * The search is limited to 15 tree levels to ensure proper performance.
 *  
 * If this module saves you time and effort, please contribute to the project at
 * http://www.idxsoft.com/contribute
  
 * This program is free software; you can redistribute it and/or modify it 
 * under the terms of the GNU General Public
 *  License as published by the Free Software Foundation version 2
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY;  
 * without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  
 * See the GNU General Public License for more details.
 *
 */

 /**
  * Function  pvh - Shorthand for phpvariable_hunter.
  */
function pvh($array, $needle, $exact_match = FALSE) {
  $ret = phpvariable_hunter($array, $needle, $exact_match);
  return $ret;
}

/**
 * Function phpvariable_hunter - performs a nested variable lookup.
 */
function phpvariable_hunter($array, $needle, $exact_match = FALSE) {
  $hunter_varname = phpvariable_hunter_getcaller(1);
  if ($hunter_varname = 'array') {
    $hunter_varname = phpvariable_hunter_getcaller(2);
  }
  $hunter_stack = array();
  $hunter_types = array();
  $hunter_results = array();
  $ret = phpvariable_hunter_internalcall($array, $needle, $exact_match, $hunter_varname, $hunter_stack, $hunter_results, $hunter_types);
  unset($hunter_stack);
  unset($hunter_types);
  // Now return the results...... both to variable and screen.
  $ret = $hunter_results;
  // A header to find what we searched for.
  // MUST use PRINT() for theme compatiblity. OMEGA does not like Krumo.
  // This is open to @todo
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
function phpvariable_hunter_internalcall($array, $needle, $exact_match, $hunter_varname, &$hunter_stack, &$hunter_results, &$hunter_types) {
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
            $answer = '$' . $hunter_varname;
            $wasobject = FALSE;
            foreach ($hunter_stack as $key_stack => $var) {
              // Have to determine if the prior entry was a class.
              if (isset($hunter_types[$key_stack]) && $hunter_types[$key_stack] == 'object') {
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
              $hunter_results[] = $answer . ';  ==  ' . $val;
            }
          }
        }
        else {
          if (count($hunter_stack) < 15) {
            // Save the key before tracing down.
            $hunter_types[count($hunter_stack)+1] = gettype($val);
            $hunter_stack[count($hunter_stack)+1] = $key;
            phpvariable_hunter_internalcall($val, $needle, $exact_match, $hunter_varname, $hunter_stack, $hunter_results, $hunter_types);
            unset($hunter_types[count($hunter_stack)]);
            unset($hunter_stack[count($hunter_stack)]);
          }
          else {
            // Depth greater than search limitation, continue with the loop.
                continue;
          }
        }
      }
      return $ret;
    }
  }
  catch (Exception $e) {
    unset($hunter_types[count($hunter_stack) - 1]);
    unset($hunter_stack[count($hunter_stack) - 1]);
  }
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
        return $ret;
      }
    }
  }
}
