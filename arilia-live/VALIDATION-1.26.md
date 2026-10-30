# Arilia 1.26 validation

Based on the latest 1.25 archive. Nine supplied images converted to WebP without changing the composition. All seven individual bottle images are 1024 x 1536. The banner is 1900 x 828; gym poster is 1024 x 1536.

Verified: all nine new images decode; all literal asset references in PHP resolve; new bottle URLs cover product cards, detail pages and saved thumbnails; banner maps to the Flavours hero and collection image; men card replaces legacy video with supplied poster; source ZIP compared against the baseline; archive CRC and exact packaged bytes verified.

Changed files:
- UPDATE-1.26.txt
- style.css
- functions.php
- assets/audience-men-v126.webp
- assets/bottle-strawberry-v126.webp
- assets/bottle-pomegranate-apple-v126.webp
- assets/bottle-dragon-fruit-v126.webp
- assets/bottle-beet-lime-ginger-v126.webp
- assets/flavours-poster-v126.webp
- assets/bottle-pomegranate-v126.webp
- assets/bottle-mango-v126.webp
- assets/bottle-cranberry-v126.webp
- inc/audience.php
- inc/content.php

Limitations: PHP/WordPress runtime and browser rendering were not tested because PHP and a browser binary are unavailable in this environment. No live upload or publishing performed. The desktop banner is shown whole on mobile rather than using an outdated portrait. Existing saved page text is preserved. Old branding, Juice/Lime wording and 340 mL are baked into the supplied lineup/gym artwork and were not edited.

Installation: upload this theme ZIP in WordPress and replace the current Arilia theme; clear the hosting cache. Do not recreate starter pages. Check Flavours, all seven product pages, Our Story and the gym section after installation. Roll back with the previous archive or hosting backup.
