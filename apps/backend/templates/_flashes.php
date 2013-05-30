<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php foreach ($sf_user->getAttributeHolder()->getAll('symfony/user/sfUser/flash') as $flash => $message): ?>

  <div id="flash_<?php echo $flash ?>" class="flash <?php echo $flash ?>">
    <?php echo __($message) ?>
  </div>

  <script>
    jQuery('div#flash_<?php echo $flash ?>').notification({'duration': 30});
  </script>

  <?php $sf_user->getAttributeHolder()->remove($flash, null, 'symfony/user/sfUser/flash') ?>

<?php endforeach ?>