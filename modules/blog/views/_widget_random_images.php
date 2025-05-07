<h3>Random Gallery Images</h3>
<div class="gallery">
    <?php foreach ($rows as $row) : ?>
    <img class="thumb" 
         src="<?= out($row->thumb) ?>" 
         alt="<?= strip_tags(htmlspecialchars_decode($row->title)) ?>" 
         data-full="<?= out($row->picture) ?>" 
         data-title="<?= strip_tags(htmlspecialchars_decode($row->title)) ?>" 
         data-url="<?= BASE_URL ?>blog/post/<?= out($row->url_string) ?>">
    <?php endforeach; ?>
</div>

<div class="modal" id="modal">
    <div class="modal-content">
        <a id="modal-link" href="">
            <span id="modal-title" class="text-large"></span> <i class="fa fa-share-square-o"></i>
        </a>
        <span class="close">×</span>
        <img id="modal-image" src="" alt="">
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const thumbs = document.querySelectorAll('.thumb');
    const modal = document.getElementById('modal');
    const modalImage = document.getElementById('modal-image');
    const modalLink = document.getElementById('modal-link');
    const modalTitle = document.getElementById('modal-title');
    const closeModal = modal.querySelector('.close');

    // Öffnet das Modal
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            const fullImageSrc = thumb.dataset.full;
            const title = thumb.dataset.title;
            const url = thumb.dataset.url;

            modalImage.src = fullImageSrc;
            modalImage.alt = title;
            modalLink.href = url;
            modalTitle.textContent = 'Read: '+title;

            modal.classList.add('show');
        });
    });

    // Schließt das Modal
    closeModal.addEventListener('click', () => {
        modal.classList.remove('show');
    });

    // Schließt das Modal bei Klick außerhalb des Bildes
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });
});
</script>
<style>
.gallery {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: stretch;
    justify-content: center;
}

.thumb {
    width: 130px;
    height: 130px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.thumb:hover {
    transform: scale(1.1);
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal.show {
    display: flex;
    opacity: 1;
}

.modal-content {
    background-color: white;
    position: relative;
    max-width: 90%;
    max-height: 90%;
    text-align: center;
    overflow: auto;
}

#modal-image {
    width: 100%;
    height: auto;
}

.close {
    position: absolute;
    top: 10px;
    right: 10px;
    color: white;
    font-size: 2rem;
    cursor: pointer;
}

#modal-title {
    /*color: white;*/
    display: block;
    margin-top: 1rem;
    font-weight: bold;
}
#modal-title:hover {
    color: inherit;
}
</style>