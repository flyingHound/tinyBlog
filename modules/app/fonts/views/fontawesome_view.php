<?= $mod_nav ?>
<h2 class="heading"><?= out($headline) ?></h2>

<?= $output ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const iconContainers = document.querySelectorAll('.icon-container');
    iconContainers.forEach(function(container) {
        container.addEventListener('mouseenter', function() {
            showCopyButton(this);
        });
        container.addEventListener('mouseleave', function() {
            hideCopyButton(this);
        });
    });
});

function showCopyButton(container) {
    let copyButton = container.querySelector('.copy-button');
    if (!copyButton) {
        copyButton = document.createElement('a');
        copyButton.className = 'copy-button';
        copyButton.textContent = 'Copy';
        copyButton.onclick = function() {
            copyToClipboard(container);
        };
        container.appendChild(copyButton);
    }
    copyButton.style.display = 'block';
}

function hideCopyButton(container) {
    const copyButton = container.querySelector('.copy-button');
    if (copyButton) {
        copyButton.style.display = 'none';
    }
}

function copyToClipboard(container) {
    const iconRef = container.querySelector('.icon-ref');
    const iconHtml = iconRef.getAttribute('data-icon-html');
    if (iconHtml) {
        navigator.clipboard.writeText(iconHtml).then(function() {
            const message = document.createElement('div');
            message.className = 'copy-message';
            message.textContent = 'Copied: ' + iconHtml;
            container.appendChild(message);
            setTimeout(function() {
                message.remove();
            }, 2000);
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
        });
    } else {
        console.error('No icon HTML found to copy');
    }
}

function filterIcons() {
    const searchValue = document.getElementById('icon-search').value.toLowerCase();
    const iconContainers = document.querySelectorAll('.icon-container');

    iconContainers.forEach(container => {
        const iconName = container.querySelector('.icon-name h6').textContent.toLowerCase();
        if (iconName.includes(searchValue)) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    });

    // Kategorien ein-/ausblenden basierend auf sichtbaren Icons
    const grids = document.querySelectorAll('.icon-grid');
    grids.forEach(grid => {
        const visibleIcons = grid.querySelectorAll('.icon-container:not([style*="display: none"])');
        grid.style.display = visibleIcons.length > 0 ? 'grid' : 'none';
        const categoryTitle = grid.previousElementSibling;
        if (categoryTitle && categoryTitle.classList.contains('category-title')) {
            categoryTitle.style.display = visibleIcons.length > 0 ? 'block' : 'none';
        }
    });
}
</script>

<style>
/* Filter Styles */
.filter-container {
    padding: 20px 0;
    text-align: center;
}

#icon-search {
    padding: 10px;
    width: 300px;
    max-width: 100%;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

/* Copy to Clipboard Styles */
.icon-container {
    position: relative;
}

.copy-button {
    background-color: #007bff;
    border: 1px solid #0056b3;
    border-radius: 4px;
    padding: 0.4em 0.6em;
    color: #fff;
    font-size: 0.65em;
    cursor: pointer;
    text-decoration: none;
    font-family: Arial, sans-serif;
    transition: opacity 0.3s;
    display: none;
    position: absolute;
    top: 0.5rem;
    right: 0.4rem;
}

.copy-button:hover {
    color: rgba(0, 0, 0, 0.7);;
}

.copy-message {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    min-width: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px;
    text-align: center;
    font-size: 9px;
}

/* Icon Grid Styles */
.icon-grid-container {
    padding: 0 20px;
}

.category-title {
    font-size: 1.2em;
    margin: 20px 0 10px;
    color: #333;
}

.icon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 20px;
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 10px;
}

.icon-container {
    text-align: center;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

.icon-container:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.icon-name {
    padding: 10px;
    font-weight: bold;
}

.icon {
    font-size: 24px;
    padding: 20px;
    transition: all 0.2s ease;
}

.icon-ref {
    padding: 10px;
    color: #666;
    font-size: 12px;
}

.neon-glow {
    color: #000A33;
    transition: text-shadow 0.3s ease;
}

.neon-glow:hover {
    text-shadow: 0 0 10px hsl(186, 100%, 69%),
                 0 0 20px hsl(186, 100%, 69%),
                 0 0 30px hsl(186, 100%, 69%),
                 0 0 40px hsl(186, 100%, 69%);
}
</style>