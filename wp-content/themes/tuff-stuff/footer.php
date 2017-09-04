<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package mttr
 */

?>

	</div><!-- #content -->

	<footer class="site-footer">

	<?php 
	
      mttr_get_template( 'template-parts/footer/_c.newsletter-bar' );
      mttr_get_template( 'template-parts/footer/_c.footer' );
      mttr_get_template( 'template-parts/footer/_c.colophon-a' );

    ?>

	</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
