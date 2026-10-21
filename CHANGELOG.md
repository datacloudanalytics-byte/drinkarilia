# Changelog

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
