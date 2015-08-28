<?php if ( $pathways = $course->getPathways() ): ?>
<ul<?php if (isset($id)): ?> id="<?php echo $id; ?>"<?php endif; ?>>
<?php foreach ($pathways as $a_pathway): ?>
    <li><?php echo $a_pathway; ?></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<span class="alert"><?php echo __('Sin trayectorias para el año %s', array('%s'=> $course->getSchoolYear() ) ); ?></span>
<?php endif; ?>