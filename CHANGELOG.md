# Changelog

## Live 1.x line — 1.29.0 — 2026-10-30

- Our Story gains a pomegranate heritage section ("A little fruit. A long Indian story.") between the origin story and the fruit-to-bottle steps: supplied poster shown whole, supplied copy as live text.

## Live 1.x line — 1.28.0 — 2026-10-30

- Adopted the supplied 1.27 theme as the base (new logo, bottle photos, posters).
- Our Story page rebuilt on the structure of POM Wonderful's Our Story page: hero, origin story, four-step "Fruit to bottle" process, headline numbers band, "More than meets the label" details, lifestyle poster gallery (posters shown whole), values and Stock Arilia CTA. Applied at render time to the saved page; editable blocks on new imports. Copy in `arilia-live/inc/about.php`.

## Live 1.x line — 1.22.0 — 2026-10-28

- Homepage slider gains Dragon Fruit Drink and Mango Drink slides (landscape on desktop, portrait under 1024px), each linking to its flavour page. Six slides, 3 s each.

## Live 1.x line — 1.21.0 — 2026-10-28

- Homepage slider gains Cranberry Drink and Strawberry Drink slides (landscape on desktop, portrait under 1024px), each linking to its flavour page. Four slides, 3 s each.

## Live 1.x line — 1.20.0 — 2026-10-28

- Images shown whole everywhere: audience photos re-cut to one 4:3 frame from the full originals, square gym video letterboxed in the same frame, seniors card uses the complete camping photo (separate family banner removed). Product cards and flavour pages now show whole bottle cut-outs instead of masked crops of the group photo. Stale saved card media is refreshed at render time. Browser audit: zero cropped images on home, Flavours, story and flavour pages at desktop and phone widths.

## Live 1.x line — 1.19.0 — 2026-10-28

- Homepage lineup section ("Seven ways to find your wonderful") removed; it duplicated the audience section. Hidden at render time on saved pages and omitted from new imports.

## Live 1.x line — 1.18.0 — 2026-10-28

- Every product is a "Drink" in copy, alt text and the audience section; no "Juice" wording remains (artwork unchanged). Same change applied to the 2.0 theme catalogue.

## Live 1.x line — 1.17.0 — 2026-10-28

- Previous / next arrows on the homepage slider (sides on desktop, bottom corners on phones); left/right arrow keys work when the slider has focus.

## Live 1.x line — 1.16.0 — 2026-10-28

- Homepage hero is a two-slide carousel (Pomegranate Juice, Beet + Lemon + Ginger Drink) advancing every 3 s, with dots, swipe, pause on hover/focus/hidden tab and reduced-motion stop. Each slide links to its flavour page.
- Phones and tablets (under 1024px) show the new portrait posters for both hero slides and for the Flavours page.

## Live 1.x line — 1.14.0 — 2026-10-21

- New homepage section directly below the hero poster: white benefits strip (four icons) and "A flavour for every age and every lifestyle" with five audience cards (women, men, kids, daily energy, seniors), bottle thumbnails linked to flavour pages, closing line, two buttons and the wellness disclaimer. Injected at render time on saved homepages; editable core blocks on new imports. Copy in `arilia-live/inc/audience.php`.

## Live 1.x line — 1.13.0 — 2026-10-21

- The ruby "Meet the Arilia 250 ml collection" strip is shown again on the homepage only (29px). Inner pages keep the 92px header. The logo row has a fixed 80px minimum height so logo, menu and Stock Arilia align identically on every page. Home poster still fits at 1366×700 (bottom edge 667px).

## Live 1.x line — 1.12.0 — 2026-10-21

- Compact header: announcement strip hidden, logo 118px (96px on phones), tighter padding. Header height 187px → 92px on desktop, 147px → 75px on phones, so the home hero poster and the Flavours poster fit on the first screen (verified at 1366×700, 1440×820, 390×740).
- Posters and hero/story/lineup images use `max-height: calc(100svh - 92px)`.
- Footer logo 150px. Everything else unchanged from 1.11.

## 2.0.2 — 2026-10-21

- Stylesheet now also styles pages that still hold 1.x content (hero arch, ribbon, story, connect band), so the poster hero shows before "Upgrade page layouts" is run.

## 2.0.1 — 2026-10-21

- Home hero restyled as the ruby poster: full-bleed background, bold white uppercase headline with gold accent, bottle in a tall arch that spans the full hero height, gold flavour ribbon.

## 2.0.0 — 2026-10-21

Full rebuild of the Arilia theme. See `docs/AUDIT-REPORT.md` for the audit that motivated it.

### Fixed
- Mobile heading "FRUIT.WITH APOINT OF VIEW." caused by hidden `<br>` without spaces.
- Text sizes between 7px and 10px across the site (now 13px minimum).
- Product bottles cropped from the group photo with fragile percentage offsets and a mismatched mask.
- Hero image cropping and conflicting `object-fit` rules.
- Three stacked stylesheets with 84 `!important` declarations and conflicting colour tokens.
- `theme.json` palette and fonts not matching the site.
- Images without width/height (layout shift) and hard-linked to the theme folder.
- `custom-logo` declared but never supported.
- Home ribbon listing five of seven flavours; product type (Juice/Drink) missing or contradicting the labels.
- Inconsistent version numbers across `style.css`, README and importer.
- Setup lock that could stay locked forever.
- No way to update already-imported page content.

### Added
- Design tokens, Fraunces + Manrope web fonts, one stylesheet.
- Seven transparent bottle cut-outs, favicon and logo mark.
- Rebuilt Home, Products, Our Story, Contact and seven flavour pages (tasting notes, how to serve, at a glance, related flavours).
- Plugin-free enquiry form block with spam protection and rate limiting (email only, no storage).
- Media Library import of artwork with featured images.
- "Upgrade page layouts" with revision safety; contact settings screen.
- Open Graph / Twitter meta, Organization, WebSite and Product JSON-LD, SVG favicon fallback, font preloading.
- Search template, restyled 404, blog templates.
- Sticky header with "Stock Arilia" CTA; mobile product carousels; FAQ accordion.

### Removed
- `assets/site.css`, `assets/refinement.css`, `assets/bottle-mask.svg`, `README.txt`, `UPDATE-GUIDE.txt`, `VALIDATION.md`, `FILES.md` (replaced by `readme.txt` and the docs folder).

## 1.2.0
Delivered package: colour/arch restoration layered over 1.1 refinements. Not browser-verified.

## 1.1.0
Presentation refinements layered over 1.0.

## 1.0.0
Initial block theme with starter importer.
