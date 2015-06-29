
// directory of where all the images are
var cmThemeOffice2003Base = '/JSCookMenu/ThemeOffice2003/';

// the follow block allows user to re-define theme base directory
// before it is loaded.
try
{
	if (myThemeOffice2003Base)
	{
		cmThemeOffice2003Base = myThemeOffice2003Base;
	}
}
catch (e)
{
}

var cmThemeOffice2003 =
{
	prefix:	'ThemeOffice2003',
  	// main menu display attributes
  	//
  	// Note.  When the menu bar is horizontal,
  	// mainFolderLeft and mainFolderRight are
  	// put in <span></span>.  When the menu
  	// bar is vertical, they would be put in
  	// a separate TD cell.

  	// HTML code to the left of the folder item
  	mainFolderLeft: '&nbsp;',
  	// HTML code to the right of the folder item
  	mainFolderRight: '&nbsp;',
	// HTML code to the left of the regular item
	mainItemLeft: '&nbsp;',
	// HTML code to the right of the regular item
	mainItemRight: '&nbsp;',

	// sub menu display attributes

	// 0, HTML code to the left of the folder item
	folderLeft: '<img alt="" src="' + cmThemeOffice2003Base + 'spacer.gif">',
	// 1, HTML code to the right of the folder item
	folderRight: '<img alt="" src="' + cmThemeOffice2003Base + 'arrow.gif">',
	// 2, HTML code to the left of the regular item
	itemLeft: '<img alt="" src="' + cmThemeOffice2003Base + 'spacer.gif">',
	// 3, HTML code to the right of the regular item
	itemRight: '<img alt="" src="' + cmThemeOffice2003Base + 'blank.gif">',
	// 4, cell spacing for main menu
	mainSpacing: 0,
	// 5, cell spacing for sub menus
	subSpacing: 0,

	// move 1st lvl submenu for horizontal menus up a bit to avoid double border
	offsetHMainAdjust:	[0, -1],
	offsetVMainAdjust:	[-1, 0],
	// offset according to Opera, which is correct.
	offsetSubAdjust:	[1, 0]
	// rest use default settings
};

// for horizontal menu split
var cmThemeOffice2003HSplit = [_cmNoClick, '<td class="ThemeOffice2003MenuItemLeft"></td><td colspan="2"><div class="ThemeOffice2003MenuSplit"></div></td>'];
var cmThemeOffice2003MainHSplit = [_cmNoClick, '<td class="ThemeOffice2003MainItemLeft"></td><td colspan="2"><div class="ThemeOffice2003MenuSplit"></div></td>'];
var cmThemeOffice2003MainVSplit = [_cmNoClick, '|'];
