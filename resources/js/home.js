document.addEventListener("DOMContentLoaded", () => {
    // Hero banner modal (if present)
    const heroBannerEl = document.getElementById('heroBannerModal');
    if (heroBannerEl && typeof bootstrap !== 'undefined') {
        const bannerModal = new bootstrap.Modal(heroBannerEl, { backdrop: 'static' });
        bannerModal.show();
    }

    const modal = new bootstrap.Modal(document.getElementById('projectModal'));
    const modalTitle = document.getElementById('projectTitle');
    const modalDesc = document.getElementById('projectDescription');
    const modalLink = document.getElementById('fullProjectLink');
    const galleryBox = document.getElementById('lightboxGallery');

    const lightbox = document.getElementById('lightboxOverlay');
    const lightboxImage = document.getElementById('lightboxImage');
    const nextBtn = document.querySelector('.lightbox-next');
    const prevBtn = document.querySelector('.lightbox-prev');
    const closeBtn = document.querySelector('.lightbox-close');

    let allImages = [];
    let currentIndex = 0;

    document.querySelectorAll('.view-project-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const title = btn.dataset.title;
            const desc = btn.dataset.description;
            const image = btn.dataset.image;
            const gallery = JSON.parse(btn.dataset.gallery || '[]');
            const id = btn.dataset.id;

            modalTitle.textContent = title;
            modalDesc.textContent = desc;
            modalLink.href = `/projects/${id}`;

            galleryBox.innerHTML = "";

            allImages = [image, ...gallery];

            allImages.forEach((src, index) => {
                const img = document.createElement('img');
                img.src = src;
                img.classList.add('lightbox-thumb', 'rounded', 'shadow');
                img.style.width = '170px';
                img.style.height = '130px';
                img.style.objectFit = 'cover';
                img.loading = 'lazy';
                img.decoding = 'async';
                img.onclick = () => openLightbox(index);
                galleryBox.appendChild(img);
            });

            modal.show();
        });
    });

    const openLightbox = (index) => {
        currentIndex = index;
        lightboxImage.src = allImages[currentIndex];
        lightbox.style.display = 'flex';
    };

    const closeLightbox = () => {
        lightbox.style.display = 'none';
    };

    const nextImage = () => {
        currentIndex = (currentIndex + 1) % allImages.length;
        lightboxImage.src = allImages[currentIndex];
    };

    const prevImage = () => {
        currentIndex = (currentIndex - 1 + allImages.length) % allImages.length;
        lightboxImage.src = allImages[currentIndex];
    };

    nextBtn?.addEventListener('click', nextImage);
    prevBtn?.addEventListener('click', prevImage);
    closeBtn?.addEventListener('click', closeLightbox);
    lightbox?.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
});
