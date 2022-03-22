<?php defined('ABSPATH') or die;
/**
 * Título de la vista
 */
?>
<h1 class="title"><?php echo is_admin() ? get_admin_page_title() : get_the_title(); ?></h1>