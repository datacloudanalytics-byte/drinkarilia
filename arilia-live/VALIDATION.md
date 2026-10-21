# Arilia review

Live baseline visually inspected: homepage and Products at desktop width.
Mango detail navigation checked. All seven catalogue image sources loaded.
Catalogue had no horizontal overflow at the observed desktop viewport.
Observed issues: oversized headings, cropped homepage artwork, narrow image
strips in product cards, uneven CTA baselines, undersized footer wordmark.

1.1 changes are presentation-only. Page generation, content and templates are
unchanged from the delivered 1.0 ZIP. Responsive rules inspected in source.
ZIP integrity and CSS syntax checked during packaging.

Not verified: revised layout in a browser, mobile menu interaction, all live
links, email/WhatsApp delivery, field performance, hosting deployment.
The cloud browser could not access the local preview server.
Earlier runtime/block tests belong to version 1.0, not a new 1.1 runtime run.
The live website has not been updated by this package.

1.2: restored original colour/arch direction per user request. CSS parsed;
archive checked. Revised browser appearance still unverified; not deployed.

1.3: header links and CTA updated to supplied reference. Source/CSS/package
checked; live deployment and mobile interaction remain untested.

1.4: scoped story styling, viewport-limited uncropped imagery, brand-line
render filter for saved headers. CSS and package checks passed. Revised
browser rendering and live installation not yet verified.

1.5: supplied logo inspected, WebP verified, CSS parsed, archive checked.
Header/footer source and saved-wordmark handling checked. Browser appearance
and live deployment remain unverified.

1.6: logo source visually inspected; lossless WebP alpha verified; archive
checked. Live deployment and browser appearance remain unverified.

1.7: reviewed user desktop screenshot; CSS composition updated. CSS parsed
and ZIP integrity checked. New layout not browser-verified or deployed.

1.8: inspected all eight supplied images. Selected matching 250 ml portraits
and seven-flavour lineup; preserved existing asset dimensions and visually
reviewed all seven existing crop windows against the refreshed lineup. WebP
decoding and ZIP integrity checked. No CSS or layout changes. Live deployment
and full browser rendering of this update remain unverified.

1.9: WordPress Playground PHP runtime passed 11 legacy image cases covering
hero, story, lineup, all seven flavours and a legacy asset URL. Verified
stale srcset removal, alt preservation and unrelated-image preservation.
New artwork files are byte-identical to the approved 1.8 replacements.
ZIP integrity checked. Live inspection returned HTTP 403; not deployed.

1.10: supplied posters visually inspected. WordPress runtime checks passed
for homepage poster/link, Products poster and preservation of other page
intros. CSS parsed; ZIP integrity checked. Full browser appearance and live
deployment remain unverified. Supplied hero label says 340 ml.

1.11: source banner visually inspected, dimensions verified (1983 x 793),
WebP decoded and packaged asset reference checked. ZIP integrity checked.
Existing banner rendering logic unchanged. Not deployed to the live site.
1.12: header compaction verified in a browser at 1366x700, 1440x820 and 390x740 on Home, Flavours and Mango (local WordPress 7.1).
