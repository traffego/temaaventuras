<?php
/**
 * comments.php – Template de comentários
 *
 * @package TemaAventuras
 */

if ( post_password_required() ) return;

// Callback definido ANTES de wp_list_comments
if ( ! function_exists( 'tema_aventuras_comment' ) ) :
function tema_aventuras_comment( $comment, $args, $depth ) {
    $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comentario-item', $comment ); ?>
        style="background:var(--fundo-card);border:1px solid var(--borda-glass);border-radius:var(--raio-xl);padding:var(--espaco-xl);">
        <div style="display:flex;gap:var(--espaco-md);align-items:flex-start;margin-bottom:var(--espaco-md);">
            <div style="border-radius:50%;overflow:hidden;flex-shrink:0;">
                <?php echo get_avatar( $comment, 48, '', get_comment_author(), [ 'class' => '' ] ); ?>
            </div>
            <div>
                <strong style="color:var(--texto-primario);"><?php comment_author(); ?></strong>
                <div style="font-size:0.75rem;color:var(--texto-muted);">
                    📅 <?php comment_date( 'd M Y' ); ?> às <?php comment_time(); ?>
                </div>
            </div>
        </div>
        <?php if ( '0' === $comment->comment_approved ) : ?>
            <p style="color:var(--texto-muted);font-style:italic;font-size:var(--tamanho-pequeno);">
                <?php _e( 'Seu comentário está aguardando moderação.', 'temaaventuras' ); ?>
            </p>
        <?php endif; ?>
        <div class="comment-content wp-content"><?php comment_text(); ?></div>
        <div style="margin-top:var(--espaco-sm);">
            <?php comment_reply_link( array_merge( $args, [
                'reply_text' => '↩ ' . __( 'Responder', 'temaaventuras' ),
                'depth'      => $depth,
                'max_depth'  => $args['max_depth'],
            ] ) ); ?>
        </div>
    </<?php echo $tag; ?>>
    <?php
}
endif;
?>

<div id="comentarios" class="comentarios-area">

    <?php if ( have_comments() ) : ?>
        <h2 class="comentarios-titulo" style="font-size:1.8rem;margin-bottom:var(--espaco-2xl);">
            <?php
            $count = get_comments_number();
            printf(
                _n( '%1$s Comentário em "%2$s"', '%1$s Comentários em "%2$s"', $count, 'temaaventuras' ),
                number_format_i18n( $count ),
                get_the_title()
            );
            ?>
        </h2>

        <ol class="comment-list" style="list-style:none;display:flex;flex-direction:column;gap:var(--espaco-lg);">
            <?php
            wp_list_comments( [
                'style'      => 'ol',
                'short_ping' => true,
                'callback'   => 'tema_aventuras_comment',
            ] );
            ?>
        </ol>

    <?php endif; ?>

    <?php
    comment_form( [
        'title_reply'       => __( 'Deixe um Comentário', 'temaaventuras' ),
        'title_reply_to'    => __( 'Responder para %s', 'temaaventuras' ),
        'cancel_reply_link' => __( 'Cancelar', 'temaaventuras' ),
        'label_submit'      => __( 'Enviar Comentário', 'temaaventuras' ),
        'comment_field'     => '<div class="form-grupo"><label for="comment">' . __( 'Comentário *', 'temaaventuras' ) . '</label><textarea id="comment" name="comment" required rows="6" placeholder="' . __( 'Compartilhe sua experiência...', 'temaaventuras' ) . '"></textarea></div>',
        'class_submit'      => 'btn btn--primario',
        'submit_button'     => '<button type="submit" id="%1$s" class="%2$s" style="margin-top:var(--espaco-md);">💬 %4$s</button>',
    ] );
    ?>

</div>
