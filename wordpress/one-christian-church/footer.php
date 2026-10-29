<?php
/**
 * Site footer: the "Site footer" synced pattern (Appearance > Patterns), then the scripts.
 *
 * @package One_Christian_Church
 */

occ_footer();
?>
<?php if ( get_theme_mod( 'occ_preview_badge', false ) ) : ?><div class="preview-pill" data-preview-pill role="note"><span><b>Design preview.</b> Videos are placeholders and forms are mock-ups.</span><button type="button" aria-label="Hide this note">&times;</button></div><?php endif; ?>
<?php occ_video_list(); ?>
<?php wp_footer(); ?>
</body>
</html>
