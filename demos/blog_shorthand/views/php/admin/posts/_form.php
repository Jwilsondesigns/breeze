<form method="post" action="<?php h($back); ?>">
    <fieldset>

<?php if (!empty($post)): ?>
        <input type="hidden" name="_method" value="PUT" id="_method">
<?php endif; if (isset($flash['post'])) $post = $flash['post']; ?>

        <p<?php if (isset($flash['errors']['title'])): ?> class="error"<?php endif; ?>>
            <label for="post_title">Title</label>
            <input type="text" name="post[title]" value="<?php if (isset($post['title'])) h($post['title']); ?>" id="post_title">
        </p>

        <p<?php if (isset($flash['errors']['contents'])): ?> class="error"<?php endif; ?>>
            <label for="post_contents">Contents</label>
            <textarea name="post[contents]" id="post_contents" rows="8" cols="40"><?php if (isset($post['contents'])) h($post['contents']); ?></textarea>
        </p>

        <p>
            <input type="submit" value="<?php h($button); ?>">
            or <a href="<?php h($back); ?>">cancel</a>
        </p>

    </fieldset>
</form>