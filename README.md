# Navbar Walker Wordpress

Custom Walker for the wordpress menu structure.

# How to use 

1. Download the `navbar.php` file and copy and paste it into the theme.

```bash
git clone https://github.com/Rayiumir/navbar-walker-wordpress.git
cd navbar-walker-wordpress/
```
2. Calling the file in `functions.php`.

```php
require_once('navbar.php');
```

3. Register a new menu by adding the follow code into the `functions.php` file of your theme.

```php
register_nav_menu('menu-one', 'Menu Header');
```

4. Sample HTML menu code:

```html
<ul class="nav-menu">
    <li class="nav-item">
        <a href="#" class="nav-link">Home</a>
    </li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-link">Links</a>
        <div class="dropdown-menu">
            ...
        </div>
    </li>
</ul>
```

5. Add the following html code in your `header.php` file or wherever you want to place your menu.

```php
<ul class="nav-menu">
    <?php
        wp_nav_menu(array(
            'theme_location' => 'menu-one',
            'container'      => false, // Do not wrap in a div
            'items_wrap'     => '%3$s', // Only output the list items, not the <ul> wrapper
            'menu_class'     => '', // Do not add a class to the ul itself, as we already have one
            'walker'         => new Navbar_Walker(), // Use our custom walker
            'depth'          => 2, // Allow for dropdowns (adjust as needed for deeper levels)
        ));
    ?>
</ul>
```
