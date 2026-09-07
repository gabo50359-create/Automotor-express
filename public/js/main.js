document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.alternador-menu');
  const nav = document.querySelector('.menu-navegacion');
  const backToTop = document.querySelector('.volver-arriba');

  const tabButtons = document.querySelectorAll('[data-tab-trigger]');
  const tabPanels = document.querySelectorAll('[data-tab-panel]');

  const activateTab = (tabName) => {
    tabButtons.forEach((button) => {
      const isActive = button.dataset.tabTrigger === tabName;
      button.classList.toggle('activo', isActive);
      button.setAttribute('aria-selected', String(isActive));
    });

    tabPanels.forEach((panel) => {
      const isActive = panel.dataset.tabPanel === tabName;
      panel.classList.toggle('activo', isActive);
      panel.setAttribute('aria-hidden', String(!isActive));
    });
  };

  tabButtons.forEach((button) => {
    button.addEventListener('click', () => {
      activateTab(button.dataset.tabTrigger);
    });
  });

  activateTab('carros');

  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      nav.classList.toggle('abierto');
      toggle.classList.toggle('activo');
    });
  }

  const catalogGrid = document.querySelector('.cuadricula-productos');

  if (catalogGrid) {
    const searchInput = document.querySelector('.barra-catalogo input');
    const categorySelect = document.querySelector('.barra-catalogo select');
    const categoryRadios = document.querySelectorAll('.filtro-catalogo input[type="radio"]');
    const products = [...catalogGrid.querySelectorAll('.producto, .tarjeta-producto')];
    const categoryNames = ['motor', 'frenos', 'suspension', 'electrico', 'filtros', 'llantas', 'aceites', 'carroceria'];

    products.forEach((product, index) => {
      if (!categoryNames.some((category) => product.classList.contains(`cat-${category}`))) {
        product.classList.add(`cat-${categoryNames[Math.floor(index / 3)] || 'motor'}`);
      }
    });

    const normalizeCategory = (value) => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

    const updateCatalog = () => {
      const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';
      const selectedRadio = document.querySelector('.filtro-catalogo input[type="radio"]:checked');
      const selectedCategory = selectedRadio ? selectedRadio.id.replace('filtro-', '') : 'todos';
      const selectedOption = categorySelect ? normalizeCategory(categorySelect.value) : 'todos';
      const category = selectedCategory !== 'todos' ? selectedCategory : selectedOption;

      products.forEach((product) => {
        const productText = product.textContent.toLowerCase();
        const matchesSearch = !searchTerm || productText.includes(searchTerm);
        const matchesCategory = category === 'todos' || product.classList.contains(`cat-${category}`);
        product.hidden = !matchesSearch || !matchesCategory;
      });
    };

    searchInput?.addEventListener('input', updateCatalog);
    categorySelect?.addEventListener('change', () => {
      const selectedCategory = categorySelect.value.toLowerCase();
      const matchingRadio = document.querySelector(`#filtro-${selectedCategory}`);

      if (matchingRadio) {
        matchingRadio.checked = true;
      }
      updateCatalog();
    });
    categoryRadios.forEach((radio) => radio.addEventListener('change', updateCatalog));
    updateCatalog();
  }

  window.addEventListener('scroll', () => {
    if (backToTop) {
      backToTop.classList.toggle('mostrar', window.scrollY > 500);
    }
  });

  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', (event) => {
      const href = anchor.getAttribute('href');
      if (href && href !== '#') {
        const target = document.querySelector(href);
        if (target) {
          event.preventDefault();
          target.scrollIntoView();
        }
      }
    });
  });

  const slides = document.querySelectorAll('.carrusel-principal .diapositiva-carrusel');
  const indicators = document.querySelectorAll('.carrusel-principal .indicador');
  let currentIndex = 0;
  let carouselInterval;

  const setSlide = (index) => {
    slides.forEach((slide, i) => {
      slide.classList.toggle('activo', i === index);
      slide.setAttribute('aria-hidden', i !== index);
    });
    indicators.forEach((indicador, i) => {
      indicador.classList.toggle('activo', i === index);
    });
    currentIndex = index;
  };

  const nextSlide = () => {
    const nextIndex = (currentIndex + 1) % slides.length;
    setSlide(nextIndex);
  };

  if (slides.length > 0) {
    carouselInterval = setInterval(nextSlide, 4500);

    indicators.forEach((button, index) => {
      button.addEventListener('click', () => {
        setSlide(index);
        clearInterval(carouselInterval);
        carouselInterval = setInterval(nextSlide, 4500);
      });
    });
  }
});
