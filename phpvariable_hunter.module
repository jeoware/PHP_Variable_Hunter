<?php
//
// Author and disclaimer information must remain in this freely distributable source file.
// 
// PHP Variable Hunter - Find buried PHP variables.
// Copyright (c) 2013, Jimmy Olsen, idxSoft  http://www.idxsoft.com
// 
// Finds variables in complex variable arrays and returns the location.
// Syntax: Searching for 'Footer' in $form variable.
// $result = phpvariable_hunter($form, 'Footer', true); // or use shortcut pvh();
// Matches printed as ['admin']['nodes']['#options']['37']['title']['data']['#title'] value ==> This is the Footer
// Also returns the results in a variable array for debugging.
// The search is limited to 15 tree levels to ensure proper performance.
// 
//This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation version 2
// This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
// 
// If this module saves you time, please consider contributing at http://www.idxsoft.com/contribute
//

function pvh($array, $needle, $exact_match = false){
      phpvariable_hunter($array, $needle, $exact_match);     
}

function phpvariable_hunter($array, $needle, $exact_match = false) {
      $GLOBALS['phpvariable_hunter_depth'] = 0;
      $GLOBALS['phpvariable_hunter_stack'] = array();
      $GLOBALS['phpvariable_hunter_results'] = array();
      $ret = phpvariable_hunter_internalcall($array, $needle, $exact_match);
      unset($GLOBALS['phpvariable_hunter_depth']);
      unset($GLOBALS['phpvariable_hunter_stack']);
// now return the results...... both to varaible and screen
      $ret = $GLOBALS['phpvariable_hunter_results'];
      unset($GLOBALS['phpvariable_hunter_results']);
      // a header to find what we searched for
      print( '</br></br></br>'); // assume an adminstrator menu

      print(t('PHP Variable Hunter results for needle = ') . $needle . '</br></br>');
      if (count($ret) == 0) {
            // return nothing found
            print(t('No results found.' . '</br>'));
      }
      else {
            // return all the variabes...
            foreach ($ret as $msg) {
                  print(t('Found in ') . $msg . '</br>');
            }
                  print('</br>' . count($ret) . t( ' Matches found') .  '</br>');
            
      }
      return $ret;
}

function phpvariable_hunter_internalcall($array, $needle, $exact_match) {
      try {
            $GLOBALS['phpvariable_hunter_depth'] +=1;  
            $ret = array();
            if ((is_array($array)) || (is_object($array))) {
                  foreach ($array as $key => $val) {
                        if ((isset($val) && !is_array($val)) && !is_object($val)) {
                              // here's where we check to see if we have a match
                              $ok = false;
                              if (!$exact_match) {
                                    // Looking for the value in any part of the variable.
                                    if (is_string($val)) {
                                          $pos = strpos($val, $needle);
                                          if ($pos !== false)
                                                $ok = true;  // found it
                                    }
                                    else {
                                          if (is_numeric($val)) {
                                                if ($needle == strval($val)) {
                                                      $ok = true; // found it
                                                }
                                          }
                                    }
                              }
                              else {
                                    // looking for exact match here -- simple compare
                                    if ($needle === $val) {
                                          $ok = true;
                                    }
                              }
                              if ($ok) {
                                    $answer = '';
                                    foreach ($GLOBALS['phpvariable_hunter_stack'] as $var) {
                                          $answer .= '[' . $var . ']';
                                    }
                                    if ($answer != '') {
                                          $GLOBALS['phpvariable_hunter_results'][] = $answer . '  ==> ' . $val;
                                    }
                              }
                        }
                        else {
                              if ($GLOBALS['phpvariable_hunter_depth'] < 15) {
                                    // save the key before tracing down.
                                    $GLOBALS['phpvariable_hunter_stack'][count($GLOBALS['phpvariable_hunter_stack'])] = $key;
                                    phpvariable_hunter_internalcall($val, $needle, $exact_match);
                                    unset($GLOBALS['phpvariable_hunter_stack'] [count($GLOBALS['phpvariable_hunter_stack']) - 1]);
                              }
                              else {
                                    // depth greater than search limitation
                                    return $ret;
                              }
                        }
                  }
                  $GLOBALS['phpvariable_hunter_depth'] -=1; 
                  return $ret;
            }
      } catch (Exception $e) {
            unset($GLOBALS['phpvariable_hunter_stack'] [count($GLOBALS['phpvariable_hunter_stack']) - 1]);
            $GLOBALS['phpvariable_hunter_depth'] -=1; 
      }
}