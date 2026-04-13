document.addEventListener('click', function (e) {
    const formControl = e.target.closest('.form-control');
    if (!formControl) return;

    /*Get these data attributes from the DOM element */
    const id = formControl.dataset.id;
    const name = formControl.dataset.name;
    const price = formControl.dataset.price;

    // Handle event listener for the button with class .add-to-cart-btn
    if (e.target.classList.contains('add-to-cart-btn')) {

        // Send POST fetch request to the add_to_cart handler
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            // Package the request body with proper encoding of the 'name' string
            body: `add_to_cart=1&id=${id}&name=${encodeURIComponent(name)}&price=${price}`
        })
            .then(res => res.json())
            .then(data => {

                // Update cart counter in the DOM
                document.getElementById('cart-count').innerText = data.count;

                // Replace default 'Add to Cart' button with +/- control buttons
                formControl.innerHTML = `
                    <div class="quantity-controls" data-id="${id}">
                        <button class="qty-btn minus">−</button>
                        <span class="qty-value">1</span>
                        <button class="qty-btn plus">+</button>
                    </div>
                `;
            });
    }

    // Handler for the + quantity control button
    if (e.target.classList.contains('plus')) {
        updateQty(id, formControl, 'increase');
    }

    // Handler for the - quantity control button
    if (e.target.classList.contains('minus')) {
        updateQty(id, formControl, 'decrease');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    dateSpan = document.getElementById('date');
    dateSpan.innerText = new Date().getFullYear();

    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('nav-links');

    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
    });
    
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
        });
    });
});


function updateQty(id, formControl, action) {
    // Send POST request to update_cart endpoint
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${id}&action=${action}`
    })
        .then(res => res.json()) // return consumable JSON response
        .then(data => {
            // Update cart count value using the JSON response
            document.getElementById('cart-count').innerText = data.count;

            // If all quantity is removed, show Add button again
            if (data.quantity === 0) {
                formControl.innerHTML = `<button class="add-to-cart-btn">Add to Cart</button>`;
                return;
            }

            // Update quantity counter value between the action buttons
            formControl.querySelector('.qty-value').innerText = data.quantity;
        });
}

