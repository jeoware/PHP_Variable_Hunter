Author and disclaimer information must remain 
in this freely distributable source file.
 
PHP Variable Hunter - Find buried PHP variables.
Copyright (c) 2013, Jimmy Olsen, idxSoft LLC  http://www.idxsoft.com
Get the latest version at http://www.idxsoft.com/phpvariablehunter.
 
Finds variables in complex variable arrays and returns the references.

Syntax: $array_references = pvh($input_array, 'needle', EXACT_MATCH);

For example, $results = pvh($form, 'footer');
Searches through the array $form for case-insensitive text: 'footer'.

Matches are printed to the screen in PHP variable format as follows:
$form['admin']['nodes']['#options']['37']['title']->data; == admin/footer

Also returns results in a variable array so debugger can stay in session.
The search is limited to 15 tree levels to ensure proper performance.

If this module saves you time and effort, please contribute at
http://www.idxsoft.com/contribute


This program is free software; you can redistribute it and/or modify it 
under the terms of the GNU General Public
License as published by the Free Software Foundation version 2
This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY;  
without even the implied warranty of MERCHANTABILITY or 
FITNESS FOR A PARTICULAR PURPOSE.  
See the GNU General Public License for more details.

