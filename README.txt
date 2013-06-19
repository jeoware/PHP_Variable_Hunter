PHP Variable Hunter - Find buried PHP variables.
Copyright (c) 2013, Jimmy Olsen, idxSoft  http://www.idxsoft.com

 
Finds variables in complex variable arrays and returns the location.
Syntax: Searching for 'Footer' in $form variable.
$result = phpvariable_hunter($form, 'Footer', true); // or use shortcut pvh();
Matches printed as ['admin']['nodes']['#options']['37']['title']['data']['#title'] value ==> This is the Footer
Also returns the results in a variable array for debugging.
The search is limited to 15 tree levels to ensure proper performance.
PVH can be used in any PHP development environment by including the module in your source.
 
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation version 2
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 
If this module saves you time, please consider contributing at http://www.idxsoft.com/contribute

