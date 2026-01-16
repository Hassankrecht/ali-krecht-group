/* -----------------------------------------
   AKG CART STYLES
   Shopping Cart • Table • Totals
----------------------------------------- */

.akg-cart-wrapper {
    max-width: 1100px;
    margin: 0 auto 4rem auto;
}

/* Cart card / container */
.akg-cart-box {
    background: #0b0b0b;
    border-radius: 24px;
    border: 1px solid rgba(255,255,255,0.06);
    box-shadow: 0 18px 45px rgba(0,0,0,0.85);
    padding: 1.75rem;
}

/* Table */
.akg-cart-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: collapse;
    color: var(--akg-text-main);
}

.akg-cart-table thead {
    background: radial-gradient(circle at top, #2a2a2a 0, #111 80%);
}

.akg-cart-table thead th {
    border: none;
    padding: 0.9rem 0.75rem;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 1px;
    color: var(--akg-text-muted);
}

.akg-cart-table tbody tr {
    border-bottom: 1px solid rgba(255,255,255,0.04);
}

.akg-cart-table tbody td {
    padding: 0.9rem 0.75rem;
    vertical-align: middle;
    font-size: 0.9rem;
}

/* Product title */
.akg-cart-product-title {
    font-weight: 600;
    color: var(--akg-text-main);
}

/* Image */
.akg-cart-img {
    width: 70px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.08);
}

/* Quantity controls */
.akg-qty-box {
    display: inline-flex;
    align-items: center;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    background: #111;
    border: 1px solid rgba(255,255,255,0.06);
}

.akg-qty-btn {
    border: none;
    background: transparent;
    color: var(--akg-gold-soft);
    width: 26px;
    height: 26px;
    border-radius: 999px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.akg-qty-btn:hover {
    background: rgba(212,175,55,0.12);
}

.akg-qty-value {
    min-width: 26px;
    text-align: center;
    font-weight: 600;
    color: var(--akg-text-main);
}

/* Remove button */
.akg-cart-remove-btn {
    padding: 0.35rem 0.8rem;
    border-radius: 999px;
    border: 1px solid rgba(231,76,60,0.75);
    background: transparent;
    color: var(--akg-danger);
    font-size: 0.8rem;
    font-weight: 600;
    transition: 0.2s ease;
}

.akg-cart-remove-btn:hover {
    background: rgba(231,76,60,0.18);
    color: #ffb3a7;
}

/* Totals / Checkout */
.akg-cart-summary {
    margin-top: 1.5rem;
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 1rem 2rem;
}

.akg-cart-total-box {
    background: #0d0d0d;
    padding: 1.1rem 1.4rem;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.05);
    min-width: 260px;
}

.akg-cart-total-label {
    font-size: 0.9rem;
    color: var(--akg-text-muted);
}

.akg-cart-total-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--akg-gold-soft);
}

/* Empty cart */
.akg-cart-empty {
    background: #0b0b0b;
    border-radius: 18px;
    border: 1px dashed rgba(255,255,255,0.25);
    padding: 2.5rem 1rem;
    text-align: center;
    color: var(--akg-text-muted);
}

/* Responsive */
@media (max-width: 767.98px) {
    .akg-cart-box {
        padding: 1.1rem;
    }

    .akg-cart-table thead {
        display: none;
    }

    .akg-cart-table tbody tr {
        display: block;
        padding: 0.75rem 0;
    }

    .akg-cart-table tbody td {
        display: flex;
        justify-content: space-between;
        padding: 0.35rem 0;
        border: none;
        font-size: 0.85rem;
    }

    .akg-cart-table tbody td:first-child {
        justify-content: flex-start;
        gap: 0.75rem;
    }

    .akg-cart-summary {
        justify-content: center;
    }
}
