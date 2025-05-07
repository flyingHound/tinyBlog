<div class="tag-cloud">
    <h3>Tags</h3>
    <ul>
        <?php foreach ($data['tags'] as $tag): ?>
            <li>
                <a href="<?= BASE_URL ?>blog/tag/<?= out($tag->url_string) ?>">
                    #<?= out($tag->name) ?> (<?= $tag->post_count ?>)
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<style>
.tag-cloud h3 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: var(--prime-80);
}

.tag-cloud ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-cloud li {
    display: inline-block;
    margin: 0 0 .25rem 0;
}

.tag-cloud a {
    text-decoration: none;
    color: var(--prime-70);
    border: 1px solid var(--prime-10);
    padding: 0.3rem 0.8rem;
    border-radius: 3px;
    font-size: 0.9rem;
    transition: background 0.3s ease;
}

.tag-cloud a:hover {
    background: var(--prime-20);
}
</style>