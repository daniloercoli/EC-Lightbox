# EC Lightbox

EC Lightbox is a minimal WordPress plugin that adds a modern lightbox to galleries using [GLightbox](https://github.com/biati-digital/glightbox).

The plugin is **opt-in**: it only activates where you explicitly add a custom CSS class to a block (e.g. a Gallery block). Everywhere else, WordPress behaves normally.

---

## Features

- Uses [GLightbox](https://github.com/biati-digital/glightbox) for a clean, responsive lightbox.
- Works with WordPress core blocks (e.g. Gallery, Image).
- Opt-in activation via a custom CSS class (`ec-lightbox`).
- Groups images by gallery container (each block with `ec-lightbox` is its own gallery).
- No admin UI, no settings, no bloat – just enqueue + JS.
- Local vendor assets by default (no hard dependency on external CDNs).
- Optional support for loading GLightbox from a remote CDN via a WordPress filter.

---

## Requirements

- WordPress 5.0+
- PHP 7.0+
- A block-based setup (Gutenberg) is recommended.

---

## Installation

1. Download or clone this repository into:

   ```text
   wp-content/plugins/ec-lightbox/

2. Ensure the plugin structure looks like this:
   ```text
   ec-lightbox/
    ├─ ec-lightbox.php
    ├─ assets/
    │  ├─ ec-lightbox.js
    │  ├─ ec-lightbox.css
    │  └─ vendors/
    │     └─ glightbox/
    │        ├─ glightbox.min.js
    │        └─ glightbox.min.css
    └─ README.md


3. Activate **EC Lightbox** from the WordPress admin.

The plugin ships with **local GLightbox files already included** — no downloads needed.

---

## How to enable EC Lightbox on a Gallery block

1. Insert a **Gallery** block.
2. Select the whole gallery block (not a single image).
3. In the right sidebar:
- Under **Link settings**, set:
  - **Link to** → `None`
- Under lightbox settings:
  - **Disable** “Open in lightbox” or “Enlarge on click”.
4. Open the **Advanced** panel.
5. In **Additional CSS class(es)** enter:

---
### **Important prerequisites (MUST follow these)**

To ensure EC Lightbox overrides WordPress behavior, you must:

1. **Add the class `ec-lightbox`** to the Gallery block.
2. **Disable any click action** on the gallery, inside the GB editor:
- Do **not** enable “Enlarge on click” / “Open in lightbox”.
- Set **Link to** = `None`.
3. Make sure individual images inside the gallery do **not** have click settings assigned.

If these WP-native actions remain enabled, WordPress will attach its own click/lightbox behavior, overriding this plugin.
