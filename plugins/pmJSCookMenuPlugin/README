# pmJSCookMenuPlugin

The `pmJSCookMenuPlugin` is a wrapper for JSCookMenu: http://jscook.yuanheng.org/JSCookMenu/index.html
It provides a nice way (using the Composite Design Pattern) for creating menus.

## Installation

  * Install from repositories:
  
        [bash]
        $ svn export http://svn.symfony-project.com/plugins/pmJSCookMenuPlugin

## Usage

  * Common steps:
  
    * Load javascript an stylesheets (IE: ThemePanel)
    
            [yml]
            # in apps/<app>/config/view.yml
            stylesheets:    [main.css, /pmJSCookMenuPlugin/css/ThemePanel/theme.css]
            javascripts:    [/pmJSCookMenuPlugin/js/JSCookMenu.js, /pmJSCookMenuPlugin/js/ThemePanel/theme.js]

    * Select the theme (IE: ThemePanel)
    
            [php]
            // in apps/<app>/templates/layout.php (BEFORE include_javascript call)
            <script>
            var myBase = "<?php echo $sf_request->getUriPrefix().$sf_request->getRelativeUrlRoot() ?>/pmJSCookMenuPlugin/images/ThemePanel/";
            var cmBase = myBase;
            </script>

  * Display a menu using a yaml file:
  
    * Create a yaml file for the menu
    
            [yml]
            # in apps/<app>/config/menu.yml (or other file)
            root:
              root: true
              orientation: hbr
              theme: ThemePanel
            
              menu1:
                title: Start here
                icon: menu.png #this is found in web/images
                submenu: # a submenu for menu1
                  menu1.1:
                    title: go to google
                    url: http://www.google.com
                  menu1.2:
                    title: go to yahoo
                    description: another search engine
                    url: http://www.yahoo.com
                    target: blank
              menu2:
                title: some actions
                submenu:
                  menu2.1:
                    title: Create an object
                    credentials: [some, credentials]
                    url: module/actions
                  _cmSplit:
                  menu2.2:
                    title: another menu
                    url: module/actions

    * Display the menu
    
            [php]
            // in apps/<app>/templates/layout.php
            <?php $menu = pmJSCookMenu::createFromYaml(sfConfig::get("sf_app_config_dir")."/menu.yml") ?>
            <?php echo $menu->render() ?>

  * Display a menu programatically:
    
        [php]
        <?php $menu = new pmJSCookMenu() ?>
        <?php $menu->setTitle("Integrador")?>
        <?php $menu->setRoot() ?>
        <?php $menu->setOrientation("hbr") ?>
        <?php $menu->setTheme("ThemePanel") ?>

        <?php $menu_item = new pmJSCookMenuItem() ?>
        <?php $menu_item->setTitle("some module")->setUrl("@some_module") ?>
        <?php $menu->addChild("som_module", $menu_item) ?>

        <?php $menu_item = new pmJSCookMenuItem() ?>
        <?php $menu_item->setTitle("another_module")->setUrl("@another_module") ?>
        <?php $menu->addChild("another_module", $menu_item) ?>

        <?php $menu_item = new pmJSCookMenuItem() ?>
        <?php $menu_item->setTitle("and_another")->setUrl("@and_another") ?>
        <?php $menu->addChild("and_another", $menu_item) ?>

        <?php $menu->render() ?>