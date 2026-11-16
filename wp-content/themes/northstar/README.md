# Northstar WooCommerce Theme — Starter

This is a custom classic WordPress theme starter based on the supplied Northstar static storefront prototype.

## Included
- Theme setup and properly enqueued assets.
- Reused supplied storefront CSS.
- WordPress navigation menus and responsive header/footer.
- Homepage with real WooCommerce product shortcode output.
- Native WordPress custom meta box for homepage hero content (no ACF or custom-field plugin).
- WooCommerce theme support and a starter WooCommerce hooks module.
- Basic WordPress template hierarchy files.

## Install
1. Back up your local site first.
2. In WordPress admin, go to **Appearance → Themes → Add New → Upload Theme**.
3. Upload `northstar-woocommerce-theme.zip` and activate it.
4. Ensure WooCommerce is active and products have been created.
5. Set a static homepage under **Settings → Reading**.
6. Edit that homepage to see the **Northstar Homepage Hero** meta box.
7. Create and assign menus under **Appearance → Menus**.

## Important scope notes
This is the first installable foundation, not a complete production-ready conversion of every static demo page. The source prototype includes demo cart logic and fake products; those behaviors are not used here. WooCommerce's native product, cart, checkout, payment, inventory, and order functionality must remain authoritative.

The supplied prototype's product-type pages for subscriptions, bookings, bundles, and composite products are design references only. Those product types require the relevant WooCommerce extensions and additional implementation.

## Coding approach
- No ACF or custom-field plugin is required.
- The example meta box uses WordPress core APIs, a nonce, capability checks, sanitization, and escaped output.
- WooCommerce core templates are not copied into this theme.
- `template-parts/` is reserved for reusable presentation components as the theme grows.
- Before production, test the complete theme on a staging site and run WordPress Coding Standards / PHPCS.
