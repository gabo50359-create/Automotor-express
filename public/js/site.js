document.addEventListener('DOMContentLoaded', () => {
    // Carrusel automática para la página de inicio
    const diapositivas = document.querySelectorAll('.diapositiva');

    if (diapositivas.length > 1) {
        let indiceActual = 0;

        const mostrarDiapositiva = (indice) => {
            diapositivas.forEach((diapositiva, posicion) => {
                diapositiva.classList.toggle('activa', posicion === indice);
            });
        };

        setInterval(() => {
            indiceActual = (indiceActual + 1) % diapositivas.length;
            mostrarDiapositiva(indiceActual);
        }, 3000);
    }

    const normalize = (value) => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    const catalogForm = document.querySelector('.filtros-html, .barra-catalogo');
    const catalogProducts = document.querySelectorAll('.producto-html, .productos-html > .producto, .cuadricula-productos .producto');

    if (catalogForm && catalogProducts.length > 0) {
        const searchInput = catalogForm.querySelector('input[type="search"], input[type="text"]');
        const vehicleSelect = catalogForm.querySelector('select[name="tipo"]');
        const categorySelect = catalogForm.querySelector('select[name="categoria"]');
        const selects = catalogForm.querySelectorAll('select');
        const minPriceInput = catalogForm.querySelector('input[name="precio_minimo"]');
        const maxPriceInput = catalogForm.querySelector('input[name="precio_maximo"]');

        const updateProducts = () => {
            const searchTerm = normalize(searchInput?.value || '');
            const selectedVehicle = normalize(vehicleSelect?.value || 'todos');
            const selectedCategory = normalize(categorySelect?.value || 'todas');
            const minPrice = Number(minPriceInput?.value || 0);
            const maxPrice = Number(maxPriceInput?.value || Number.POSITIVE_INFINITY);

            catalogProducts.forEach((product) => {
                const text = normalize(product.textContent);
                const priceMatch = product.textContent.match(/\$\s*[\d.]+/);
                const price = priceMatch ? Number(priceMatch[0].replace(/[^\d]/g, '')) : 0;
                const categoryMatch = selectedCategory === '0' || selectedCategory === 'todas' || text.includes(selectedCategory);
                const vehicleMatch = selectedVehicle === 'todos' || selectedVehicle === '' || text.includes(normalize(selectedVehicle));
                const matches = !searchTerm || text.includes(searchTerm);
                const matchesPrice = price >= minPrice && price <= maxPrice;

                product.hidden = !(categoryMatch && vehicleMatch && matches && matchesPrice);
            });
        };

        [searchInput, vehicleSelect, categorySelect, minPriceInput, maxPriceInput].forEach((control) => {
            control?.addEventListener('input', updateProducts);
            control?.addEventListener('change', updateProducts);
        });

        selects.forEach((select) => {
            select.addEventListener('change', updateProducts);
        });

        catalogForm.addEventListener('submit', (event) => {
            if (catalogForm.classList.contains('filtros-html')) {
                event.preventDefault();
            }
            updateProducts();
        });

        updateProducts();
    }

});
