# Arilia WordPress theme (drinkarilia.com)

> **Two theme lines live here.** `arilia-live/` is the 1.x theme that currently runs on drinkarilia.com (poster artwork, embossed logo); its latest build is [`dist/Arilia-WordPress-Theme-1.13.zip`](dist/Arilia-WordPress-Theme-1.13.zip) and it can be uploaded over the installed 1.11 as a drop-in replacement. `arilia/` is the 2.0 rebuild described below, delivered as `dist/Arilia-WordPress-Theme.zip`.

A bright, fruit-forward block theme for Arilia, the 250 ml pure fruit wellness collection. Version 2.0.0 is a full rebuild of the delivered 1.2 package: one design system, seven flavour pages, a plugin-free enquiry form, Media Library artwork, SEO output and a guided setup screen.

| | |
|---|---|
| Installable ZIP | [`dist/Arilia-WordPress-Theme.zip`](dist/Arilia-WordPress-Theme.zip) |
| Audit and redesign report | [`docs/AUDIT-REPORT.md`](docs/AUDIT-REPORT.md) |
| Before / after screenshots | [`docs/screenshots/`](docs/screenshots/) |
| Install and update guide | [`arilia/readme.txt`](arilia/readme.txt) |
| Changes | [`CHANGELOG.md`](CHANGELOG.md) |

## Repository layout

```
arilia/                 the theme (upload this folder as a ZIP)
  assets/css/arilia.css one stylesheet, all tokens
  assets/fonts/         Fraunces + Manrope (OFL)
  assets/images/        bottle cut-outs, photography, favicon
  blocks/enquiry-form/  server-rendered enquiry form block
  inc/products.php      catalogue, families, contact settings
  inc/content.php       starter page generators (core blocks)
  inc/parts.php         header and footer patterns
  inc/setup.php         Appearance → Arilia Setup (import, upgrade, settings)
  inc/enquiry.php       form handler (wp_mail, no database)
  inc/seo.php           meta, Open Graph, JSON-LD
  templates/, parts/    block templates and template parts
bin/build-zip.sh        builds dist/Arilia-WordPress-Theme.zip
bin/lint.sh             PHP / JSON / CSS sanity checks
docs/                   audit report and screenshots
```

## Install or update

1. Back up the site.
2. WordPress admin → Appearance → Themes → Add New → Upload Theme → choose the ZIP → Install (choose *Replace current with uploaded* when updating) → Activate.
3. Appearance → **Arilia Setup**: save contact details, create the starter pages (first install) or click **Upgrade page layouts** (update from 1.x).
4. Upload the logo (Appearance → Editor → Header → Site Logo) and a Site Icon (Settings → General).
5. Send one test enquiry from the Contact page.

Requirements: WordPress 6.6 or newer, PHP 8.0 or newer. No plugins required.

## Development

```
./bin/lint.sh        # syntax checks
./bin/build-zip.sh   # produce dist/Arilia-WordPress-Theme.zip
```

Theme code is GPL-2.0-or-later. Arilia trademarks and artwork belong to their rights holders. Fonts are licensed under the SIL Open Font License 1.1 (see `arilia/assets/fonts`).
