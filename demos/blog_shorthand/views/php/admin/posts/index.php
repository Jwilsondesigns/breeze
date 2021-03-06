<h2>Posts list</h2>

<?php echo partial('admin/posts/_message'); ?>

<?php if (!count($posts)): ?>
<p>There are no posts.  Would you like to <a href="/admin/posts/new">add some</a>?</p>
<?php else: ?>
<table>
    <tr>
        <th>Title</th>
        <th>Contents</th>
        <th>Published</th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
<?php foreach ($posts as $post): ?>
    <tr>
        <td><?php h($post['title']) ?></td>
        <td><?php h($post['contents']) ?></td>
        <td><?php h($post['created_at']) ?></td>
        <td><a href="<?php echo p($post['id']); ?>">Show</a></td>
        <td><a href="<?php echo p($post['id']); ?>/edit">Edit</a></td>
        <td><a href="<?php echo p($post['id']); ?>/delete">Delete</a></td>
    </tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
<br />
<a href="/admin/posts/new">Add post</a>