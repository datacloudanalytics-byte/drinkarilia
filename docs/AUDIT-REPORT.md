# Arilia website audit and redesign report

**Scope:** the uploaded `Arilia-WordPress-Theme_2.zip` (theme version 1.2.0, the package that styles www.drinkarilia.com) and the website it produces.
**Method:** the original theme was installed on a clean WordPress 7.1.3 instance, its starter pages were imported, and every page was rendered at desktop (1440px) and phone (390px) widths with a real browser. Code, templates, stylesheets, artwork and documentation were reviewed line by line.
**Reference:** POM Wonderful's site language (big confident headlines, bright backgrounds, product-first photography, colour-blocked flavour sections, one clear "find/stock it" action) plus current conventions of leading beverage brands in Europe, the US and India (sticky white header with a single CTA, horizontal product carousels on phones, FAQ accordions, plugin-free enquiry forms, structured data).

> Note: the live drinkarilia.com and pomwonderful.com could not be fetched from this environment because the session's network policy blocks those hosts. The audit therefore covers the theme package exactly as supplied, which is what the live site runs.

---

## 1. Bugs and errors found in version 1.2

### Rendering bugs

| # | Problem | Where | Effect |
|---|---------|-------|--------|
| 1 | Hidden line breaks glue words together on phones. The rule `.arilia-story-copy h2 br{display:none}` removes the `<br>` but the heading has no spaces around it. | `assets/refinement.css` | Home page on mobile shows **"FRUIT.WITH APOINT OF VIEW."** |
| 2 | Text sizes of 7px, 8px, 9px and 10px throughout (brand line, eyebrows, product meta, ribbon, footer note). | both stylesheets | Illegible on phones, fails accessibility guidance (WCAG 1.4.4 / 1.4.12), looks cheap. The 1.2 baseline renders 7 to 12 undersized text nodes per page. |
| 3 | Product bottles are "cropped" out of the seven-bottle group photo with percentage offsets (`left:-303.6%` etc.) and an SVG mask that does not match the real bottle silhouette. | `refinement.css`, `bottle-mask.svg` | Bottles are cut off, neighbouring bottles and stray fruit show at the card edges, the bottle rim is clipped on some cards, and any change to the image breaks every card. |
| 4 | Hero image forced into an arch with `object-fit:cover`, then overridden by a later layer with `contain`, then overridden again. | three CSS layers | Cropped bottle top in some widths; different result in the editor than on the front end. |
| 5 | Three stacked stylesheets fight each other: `site.css` (1.0), `refinement.css` (1.1) and a "1.2 restore" block appended to the same file. 84 `!important` declarations, 115 duplicated selectors, two conflicting `:root` colour definitions (`#8c1028` vs `#74162c`), and the H1 font is set to Impact, then Arial, then Georgia, then Arial again. | `assets/*.css` | Unpredictable rendering, impossible to maintain, slow to change. |
| 6 | `theme.json` palette and font families do not match the CSS (Impact as the display face, Georgia as "Brand Serif"), so editor colour and font pickers show values the site never uses. | `theme.json` | Editors cannot style new content consistently. |
| 7 | Images are output without `width`/`height` attributes and are hard-linked to the theme folder instead of the Media Library. | `inc/content.php` | Layout shift while loading, no responsive `srcset`, images break if the theme folder is renamed or the theme is replaced, and the images cannot be managed from Media. |
| 8 | The theme declares `custom-logo` and `featured-images` tags but never calls `add_theme_support('custom-logo')`, and no template uses a Site Logo block. | `functions.php`, `style.css` | The real Arilia logotype cannot be uploaded; the site shows a Georgia text wordmark that does not match the label. |

### Content and copy errors

| # | Problem | Effect |
|---|---------|--------|
| 9 | The home ribbon lists five flavours ("POMEGRANATE · MANGO · CRANBERRY · STRAWBERRY · DRAGON FRUIT") but the site sells seven. | Two products are invisible on the home page. |
| 10 | Product names are inconsistent with the labels: labels say "Pomegranate **Juice**", "Cranberry **Drink**", "Pomegranate + Apple **Drink**", the site never says Juice or Drink. | The website contradicts the pack, which matters for food-labelling compliance. |
| 11 | Mixed units: "250 ml" on the site, "250 mL" on labels; "Fruit favourite" singular vs "FRUIT FAVOURITES" headings. | Sloppy, inconsistent brand voice. |
| 12 | Hero copy "SMALL BOTTLE. BIG FRUIT ENERGY." reads as an energy-drink claim, while the label positioning is "Pure fruit wellness". | Off-brand and potentially a regulatory question. |
| 13 | Arrows (↗, ↓, ←) typed inside button labels. | Screen readers announce "north east arrow"; the characters render inconsistently across devices. |
| 14 | No contact form. The Contact page only offers a mailto, tel and WhatsApp link. | Most trade buyers will not leave the site to write an email; no structured lead capture. |
| 15 | No FAQ, no "why Arilia", no tasting notes or serving guidance on flavour pages: each flavour page is one sentence plus a legal note. | Thin content, weak SEO, nothing for a buyer to act on. |

### Technical and SEO gaps

| # | Problem | Effect |
|---|---------|--------|
| 16 | Version numbers disagree: `style.css` 1.2.0, `README.txt` "VERSION 1.0.0", `UPDATE-GUIDE.txt` says style.css is 1.1.0, the importer stores "1.0.0" in options. | Nobody can tell what is installed. |
| 17 | No Open Graph / Twitter tags, no structured data, no favicon fallback, no `theme-color`. | Links shared on WhatsApp, Instagram and LinkedIn show no image or description; no rich results. |
| 18 | The setup lock option never expires. If an import is interrupted the admin screen stays locked until someone edits the database. | Setup can become permanently stuck. |
| 19 | The importer cannot update existing pages. The 1.2 update guide tells users "Do NOT recreate starter pages", so any design change that needs new markup can never reach the live pages. | Design updates were limited to CSS hacks, which is how the three-layer stylesheet happened. |
| 20 | Web fonts are not bundled, so headlines fall back to Impact / Arial / Georgia depending on the device. | No consistent brand typography. |
| 21 | `VALIDATION.md` admits the 1.1 and 1.2 layouts were "not verified in a browser" and "not deployed". | The delivered package was never visually tested. |

---

## 2. What was rebuilt (theme version 2.0.0)

### Design system
* **Bright, professional palette** anchored on the label: Arilia ruby `#a4122e`, deep ruby for headings, cream `#fffcf7` page background, warm sand section background, mango gold accent, and a tint + accent pair for each of the seven flavours.
* **Typography:** Fraunces (display, close to the label wordmark) and Manrope (body), bundled as WOFF2 under the SIL Open Font License and declared in `theme.json`, so the editor and the front end match. Minimum text size is 13px.
* **One stylesheet** (`assets/css/arilia.css`, 275 rules, zero cascading layers, no `!important` fights) with design tokens, a spacing scale and a 4-2-1 responsive grid.
* Sticky translucent header with the brand, three links and a single **"Stock Arilia"** call to action; Site Logo block supported with automatic fallback to the wordmark.

### Pages (all core blocks, fully editable)
* **Home:** hero with the tagline "A little bottle. A world of flavour.", pomegranate photography in a soft blob, flavour ribbon naming all seven flavours, seven product cards, "Why Arilia" tiles, story split, "Stock Arilia" band.
* **Products:** page hero, sticky filter chips (Fruit favourites / Creative blends / Stock Arilia), two colour-blocked sections, cards sized consistently.
* **Seven flavour pages:** tinted hero with the bottle cut-out, label-accurate type (Juice/Drink), tasting notes, how to serve, at-a-glance table, legal label note, "three more little wonders", CTA band. Product structured data on each.
* **Our Story:** hero, story split, "Impossible to miss" bottle points, "What we stand for" values.
* **Contact & Distribution:** enquiry form, WhatsApp / call / email cards, four-question FAQ accordion.
* Search, 404, blog index and single-post templates restyled.
* Phones: product grids become snap-scrolling carousels; the header CTA collapses into the menu; no horizontal overflow on any page (verified).

### Product artwork
The seven bottles were extracted from the supplied group render as individual transparent WebP cut-outs (`assets/images/bottle-*.webp`), normalised to the same height, so cards and flavour heroes show whole bottles with a consistent shadow and tint. The two high-resolution single-bottle renders are used for the home hero, story sections and the two pomegranate cut-outs.

### Features
* **Plugin-free enquiry form** (`arilia/enquiry-form` block): emails the address set in Arilia Setup, with Reply-To set to the sender; honeypot, timing check, signed stateless token (cache-safe) and a per-IP rate limit. Nothing is stored in the database. Verified end to end on the test site.
* **Media Library import:** artwork is copied into Media on setup, so images get `srcset`, lazy loading and dimensions, and featured images are set for flavour pages.
* **Upgrade page layouts** on the setup screen: replaces 1.x page content with the 2.0 layout while WordPress keeps the previous content as a revision. Edited pages are only touched when explicitly ticked.
* **Contact settings** (email, phone, WhatsApp, copyright name, Instagram, Facebook) in one place, used by footer, buttons, form and schema.
* **SEO:** meta description, Open Graph, Twitter card, `theme-color`, Organization + WebSite JSON-LD on the home page, Product JSON-LD on flavour pages, SVG favicon fallback. All of it steps aside when Yoast, Rank Math, AIOSEO or SEOPress is active.
* Font preloading, deferred 1 KB script, no third-party requests, no cookies.
* Legacy 1.x image styles are kept so old page content still renders until it is upgraded.
* Setup lock expires after 10 minutes; version constant in one place.

### Verification performed
* PHP syntax check on every file; JSON validity; CSS brace balance.
* Fresh WordPress 7.1.3 + SQLite: activation, starter import (11 pages, 10 media items), layout upgrade, every page HTTP 200, 404 and search templates.
* Browser render of all pages at 1440px and 390px: zero console errors, zero images without dimensions, zero text under 11px, no horizontal overflow.
* Block editor opens the Contact page with the live enquiry form (no "unsupported block"); Site Editor opens the header part.
* Enquiry handler: valid submission is emailed (captured via a test hook), bad token rejected, honeypot silently discarded.

Before/after screenshots are in `docs/screenshots/`.

---

## 3. Recommended next steps (not code)

1. **Real product photography.** The group render is the only source for five of the bottles, so those cut-outs are about 200px wide. A studio shot of each bottle on white (2000px tall, consistent lighting) is the single biggest visual upgrade. Drop them in via the Media Library and the cards update.
2. **Upload the logotype** as the Site Logo (Appearance → Editor → Header) and a 512×512 Site Icon (Settings → General).
3. **Confirm label wording** per flavour (Juice vs Drink), and add ingredients, nutrition and shelf-life on each flavour page once approved by whoever owns compliance. The theme deliberately makes no health, origin or nutrition claims.
4. **Email deliverability:** after launch send one test enquiry. If it lands in spam or does not arrive, install an SMTP plugin (WP Mail SMTP or similar) with the business mailbox.
5. **Stockist content:** when retail partners exist, add a "Where to buy" page listing cities and partners; the FAQ already points buyers to contact for now.
6. **Social links:** add Instagram and Facebook URLs in Arilia Setup so they appear in the footer and in the Organization schema.
7. **Analytics and privacy:** if you add Google Analytics or Meta Pixel, add a privacy page and a cookie notice. The theme ships with no tracking.
8. **Performance on GoDaddy:** enable the host's page cache and a CDN; the theme itself ships 1.3 MB of assets total, most of it imagery.

---

## 4. Launch checklist

1. Back up the live site (files + database).
2. Appearance → Themes → Add New → Upload `dist/Arilia-WordPress-Theme.zip` → **Replace current with uploaded**.
3. Appearance → Arilia Setup: save contact details, then click **Upgrade page layouts** (previous content stays in revisions).
4. Open Home, Products, each flavour, Our Story and Contact on desktop and phone.
5. Appearance → Editor → Header: upload the logo if available. Settings → General: Site Icon.
6. Send a test enquiry from the Contact page and confirm it arrives.
7. Settings → Reading: make sure "Discourage search engines" is off. Submit `/wp-sitemap.xml` in Google Search Console.
8. Clear the GoDaddy cache and re-check in a private window.
