<?php
$post_title = out($record_obj->title);
$count = 0;
$html = '';
?>
<div class="gallery">
<?php foreach ($gallery_pics as $gallery_pic) {
    $count++;
    $picture_path = BASE_URL . out($data['picture_dir'] . '/' . $gallery_pic->picture);
    $thumb_path = BASE_URL . out($data['thumb_dir'] . '/' . $gallery_pic->picture);
    $alt_text = $post_title . ' - picture ' . $count;
    $html .= '<div class="gallery-pic">';
    $html .= '<img class="gallery-thumb" src="' . $thumb_path . '" alt="' . $alt_text . '" data-full="' . $picture_path . '" data-index="' . ($count - 1) . '">';
    $html .= '</div>';
}

echo $html;
?>
</div>

<!-- Modal -->
<div class="modal" id="gallery-modal">
    <div class="modal-content">
        <span class="modal-close">×</span>
        <button class="modal-prev">&lt;</button>
        <img id="modal-image" src="" alt="">
        <button class="modal-next">&gt;</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const thumbs = document.querySelectorAll('.gallery-thumb');
    const modal = document.getElementById('gallery-modal');
    const modalImage = document.getElementById('modal-image');
    const modalClose = modal.querySelector('.modal-close');
    const modalPrev = modal.querySelector('.modal-prev');
    const modalNext = modal.querySelector('.modal-next');
    let currentIndex = 0;
    let minHeight = Infinity;

    // Wartet, bis alle Bilder dekodiert wurden, bevor die Höhe angepasst wird
    Promise.all(Array.from(thumbs).map(img => 
        img.decode ? img.decode().then(() => img) : Promise.resolve(img)
    )).then(images => {
        images.forEach(img => {
            minHeight = Math.min(minHeight, img.clientHeight);
        });
        applyMinHeight();
    });

    // Setzt max-height auf die kleinste Höhe
    function applyMinHeight() {
        if (minHeight !== Infinity) {
            thumbs.forEach(img => img.style.maxHeight = minHeight + 'px');
        }
    }

    // Öffnet das Modal
    thumbs.forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            currentIndex = parseInt(thumb.dataset.index);
            updateModalImage();
            modal.classList.add('show');
        });
    });

    // Schließt das Modal
    modalClose.addEventListener('click', () => {
        modal.classList.remove('show');
    });

    // Klick außerhalb schließt
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });

    // Vorheriges Bild
    modalPrev.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateModalImage();
        }
    });

    // Nächstes Bild
    modalNext.addEventListener('click', () => {
        if (currentIndex < thumbs.length - 1) {
            currentIndex++;
            updateModalImage();
        }
    });

    // Tastatursteuerung (optional)
    document.addEventListener('keydown', (e) => {
        if (modal.classList.contains('show')) {
            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                updateModalImage();
            } else if (e.key === 'ArrowRight' && currentIndex < thumbs.length - 1) {
                currentIndex++;
                updateModalImage();
            } else if (e.key === 'Escape') {
                modal.classList.remove('show');
            }
        }
    });

    // Aktualisiert das Modal-Bild
    function updateModalImage() {
        const thumb = thumbs[currentIndex];
        modalImage.src = thumb.dataset.full;
        modalImage.alt = thumb.alt;
        modalPrev.style.display = currentIndex === 0 ? 'none' : 'block';
        modalNext.style.display = currentIndex === thumbs.length - 1 ? 'none' : 'block';
    }
});
</script>

<style>
/* Galerie-Thumbnails */
.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1rem;
}

.gallery-pic {
    overflow: hidden;
    border-radius: 5px;
}

.gallery-pic img {
    width: 100%;
    height: auto;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.gallery-pic img:hover {
    transform: scale(1.05);
}

.gallery-thumb {
    width: 100%;
    height: auto;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.gallery-thumb:hover {
    transform: scale(1.05);
}

/* Modal */
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
    position: relative;
    max-width: 90%;
    max-height: 90%;
    text-align: center;
    overflow: hidden;
}

#modal-image {
    width: 100%;
    height: auto;
    max-height: 80vh;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    z-index: 10;
}

.modal-prev, .modal-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 1rem;
    font-size: 1.5rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.modal-prev:hover, .modal-next:hover {
    background: rgba(0, 0, 0, 0.8);
}

.modal-prev {
    left: 10px;
}

.modal-next {
    right: 10px;
}
</style>