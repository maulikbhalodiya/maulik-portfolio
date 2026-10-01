# ROADMAP
### `maulik-portfolio` WordPress block theme · phased delivery
### Read `PLAN.md` first for the reasoning. This file is the build order and the checklist.

---

## HOW WE WORK

**Every phase ends with a commit and a green gate.** No phase starts until the
previous one is committed, verified by me reading the files, and pushed.

**Subagent lanes run in parallel only when their write scopes do not overlap.**
Anything two lanes would both touch is sequential by design. Overlapping writers
is the fastest way to lose work.

**I verify, the agent does not self-certify.** Every claim is checked with an
independent command, not the subagent's summary. Two of my own documents were
wrong this session and neither was visible without checking.

| Gate | Tool | Blocking |
|---|---|---|
| PHP standards | `phpcs` WordPress-Extra + Docs | 0 errors, 0 warnings |
| Static analysis | PHPStan level 5 | 0 errors |
| Syntax | `php -l` on every file | 0 errors |
| CSS | stylelint | clean |
| Token drift | regenerate and `git diff --exit-code` | no drift |
| WP compliance | Theme Check | 0 errors |
| Accessibility | axe-core on every template | 0 violations |
| Content rules | CI grep: dashes, mailto, raw email, SCSS enqueue | 0 hits |
| Performance | Lighthouse | mobile 95+ |

---

## PHASE 0 · Repository foundation ✅ COMPLETE

- [x] Repo initialised, 53 files, first commit
- [x] WPCS 3.4.1+ pinned, security release
- [x] `phpcs.xml.dist`, `phpstan.neon`, `.stylelintrc.json`, `eslint.config.mjs`
- [x] CI with 4 jobs: standards, assets, content rules, WP file structure
- [x] AI review on PRs, opt-in `/fix`, nightly audit creating issues
- [x] Four `.audit-prompts/` standards documents
- [x] Dependabot for composer, npm, actions
- [x] Git history clean, vendor and node_modules unstaged
- [ ] **Create the GitHub repo and push** ← next action
- [ ] Tag `v0.1.0`

**Verified independently:** phpcs clean 7/7, `composer validate --strict`
passes, all PHP valid, both block theme markers present, no legacy folders, zero
dashes.

---

## PHASE 1 · Design foundation

The single most important phase. Everything downstream reads these tokens, so
getting them wrong means redoing every page.

### 1.1 `theme.json` rewrite
- [ ] 13-value palette exactly as the design specifies
- [ ] **Two accents:** `#FACC15` for dark surfaces, **`#8A6A00`** for light
      surfaces. The second is mandatory, not optional
- [ ] Three font families: Syne, Plus Jakarta Sans, IBM Plex Mono
- [ ] `contentSize` 48rem for prose, `wideSize` 90rem for the 1440px container
- [ ] `defaultFontSizes: false`, `defaultSpacingSizes: false`,
      `defaultPalette: false`, `defaultGradients: false`: all explicit, because
      v3 flips the first two to true
- [ ] `useRootPaddingAwareAlignments: true` **paired with** an explicit
      `styles.spacing.padding` object
- [ ] **Zero border radius. Zero shadows.** In every entry
- [ ] **Append palette entries at the end. Never reorder.** Positional gettext
      context means reordering silently breaks translations
- [ ] Remove the three stray keys at the wrong nesting level (see defects below)

### 1.2 SCSS architecture
- [ ] `main.scss` as the only file with `@use` statements, never `@import`
- [ ] `abstracts/`, `base/`, `components/`, `utilities/`, each with `_index.scss`
- [ ] Four grid textures at four sizes: 48, 32, 96, 64px. **Do not unify**
- [ ] Consume colours as `--wp--preset--*` at runtime, never duplicated as
      literals
- [ ] Token generator reads `theme.json`, output committed, drift check in CI

### 1.3 Fonts
- [ ] Source woff2 for the three families, Latin subset, max 2 weights each
- [ ] **Metric-matched fallback faces** using `size-adjust`, `ascent-override`,
      `descent-override`, `line-gap-override`
- [ ] **Multiple fallbacks mandatory.** Arial does not exist on Android, so
      Roboto needs its own override set
- [ ] `font-display: swap`
- [ ] Self-hosted, no Google Fonts request. That request alone would blow the
      CLS budget

### 1.4 Block theme markers
- [ ] `templates/index.html` real content, not a placeholder
- [ ] `parts/header.html` and `parts/footer.html`, flat, block markup only
- [ ] **No PHP in any part.** Translatable content goes in `patterns/`
- [ ] Never create `block-templates/` or `block-template-parts/`

**Commit:** `feat(theme): design tokens, SCSS architecture, self-hosted fonts`

---

## PHASE 2 · Shell and homepage

### 2.1 Header
- [ ] Fixed, 64px, transparent at rest, blurred with a hairline past 24px scroll
- [ ] Six nav items, no Writing or Blog link
- [ ] Active state by amber 2px underline
- [ ] Résumé item always amber, opens the modal
- [ ] Mobile menu, `aria-expanded`, `aria-controls`, Escape closes and restores
      focus
- [ ] **No mail icon.** Every mailto becomes the contact form

### 2.2 Footer
- [ ] Pure black, 5/2/3/2 asymmetric grid. **Do not balance it**
- [ ] Group headings amber
- [ ] No email link anywhere

### 2.3 Homepage, 8 sections
- [ ] 01 Hero, name, identity line, CTAs, orbit canvas
- [ ] 02 Core Engineering Capabilities, 5 areas, click to inspect
- [ ] 03 Technical Ecosystem, 7 categories, 41 skills
- [ ] 04 Selected Work, RankKernel block plus featured projects
- [ ] 05 Professional Experience, **with verified dates**
- [ ] 06 Methodology, 5 stages
- [ ] 07 About
- [ ] 08 CTA, "Looking for a WordPress or PHP developer?" **job-seeking, keep
      it exactly**
- [ ] Background rhythm preserved: grid, solid, grid, split, solid, grid, solid,
      black
- [ ] One `h1`, clean heading order

**Commit:** `feat(home): header, footer, 8-section homepage`

---

## PHASE 3 · Case studies

The heart of the site and the largest content push.

### 3.1 Content model
- [ ] Projects are **pages with nested blocks, no CPT**
- [ ] `templates/page-project.html`, 12 sections
- [ ] Section 08 Security **derived** from flow validation notes, so it cannot
      drift from the architecture

### 3.2 Four complete case studies
- [ ] Cross-Domain Payment Architecture, enriched with the real gateway names
      Checkout.com, ConvesioPay, TailoredPay, the three add-ons built where none
      existed, 3D Secure, duplicate checks
- [ ] Employee Application Management, 10+ filter parameters, Brevo notification,
      notes, activity log, mPDF
- [ ] Secure Data Encryption, key outside WordPress root, LatePoint fields
- [ ] Identity Document OCR, passport documents, asynchronous polling

### 3.3 Four lighter entries
- [ ] Brevo API Automation, multi-language templates, PDF attachments
- [ ] Object Storage, Hetzner, WP-Cron scheduled migration
- [ ] Distance Pricing, Google Maps Distance Matrix, Stripe Payment Intents
- [ ] WooCommerce Integration

### 3.4 Removed
- [ ] **Rank Math SEO Engineering: deleted.** The site runs RankKernel. He never
      built Rank Math. Carrying a case study for a competitor's plugin was a
      liability and the design's own text admitted the confusion

### 3.5 Never appears
- [ ] No client names, including the two in the resume
- [ ] No impact section, no metrics, no percentages
- [ ] **No HIPAA claim.** A regulatory compliance claim we cannot verify
- [ ] No em or en dashes

### 3.6 Architecture diagram
- [ ] Nested blocks with real buttons, no canvas
- [ ] **Key flows by node id, not by label string.** The design matched on label
      equality, which breaks the moment a label is reworded

**Commit:** `feat(projects): 8 case studies with architecture diagrams`

---

## PHASE 4 · Remaining pages

- [ ] `/projects/` archive with **server-side filter links**, not client state
- [ ] `/about/` with verified timeline dates
- [ ] `/contact/` with the form
- [ ] `/resume/` viewer
- [ ] `/rankkernel/` architecture page, 9 subsystems, 14-edge dependency graph
- [ ] `404.html`, the design has no 404 route so this is new
- [ ] `home.html` and `single.html` for future posts, **no nav link**
- [ ] Person schema, no email

**Commit:** `feat(pages): archive, about, contact, resume, rankkernel, 404`

---

## PHASE 5 · The three greenfield items

**None of these is a port. All three are new construction and all three are
schedule risk.**

### 5.1 Contact form
- [ ] `core/form`, which WordPress 6.7 ships natively
- [ ] Intent dropdown, which the design never had
- [ ] Server-side handler, `admin_post`, nonce, capability where relevant
- [ ] Honeypot, validation, sanitised input
- [ ] **No raw email in any response or log**
- [ ] Success and error states
- [ ] Works without JavaScript

### 5.2 Résumé download
- [ ] Server-generated file, replacing client-side Blob templating which produces
      a document that does not match the site design
- [ ] Filename without a real email
- [ ] Matches the site design

### 5.3 Scroll reveals
- [ ] One mechanism, applied once
- [ ] **The prototype has no scroll reveals at all.** `motion` is in its
      dependencies and imported by no file. This is new work
- [ ] `prefers-reduced-motion` respected
- [ ] `transform` and `opacity` only, never layout properties

**Commit:** `feat(forms): contact form, resume download, scroll reveals`

---

## PHASE 6 · Components and motion

- [ ] Hero canvas in vanilla JS
- [ ] **Pause off-screen** with IntersectionObserver
- [ ] **Pause on hidden tab** with `visibilitychange`
- [ ] `await document.fonts.ready` before the first paint, or labels render in
      system mono and stay wrong
- [ ] Device pixel ratio capped at 2
- [ ] No per-frame `getBoundingClientRect`, no per-frame array allocation
- [ ] `prefers-reduced-motion` in JS, because CSS cannot stop a rAF loop
- [ ] Skills ecosystem as markup, **HTML controls are the accessible layer, SVG
      is decorative**
- [ ] RankKernel architecture as markup
- [ ] Contact signal graph as markup, keyboard parity fixed
- [ ] Résumé modal, focus trap, `inert` on background, focus restore

**Commit:** `feat(components): hero canvas, SVG diagrams, resume modal`

---

## PHASE 7 · Accessibility

Automated tooling catches roughly a third to two-fifths of real issues. The
manual pass is not optional.

- [ ] Drop `role="img"` from SVGs with focusable children, three places
- [ ] Add `role="tabpanel"` or convert filters to `role="group"` with
      `aria-pressed`
- [ ] Roving tabindex on the architecture map
- [ ] **Remove `focus:outline-none`** from the project card title
- [ ] Résumé reachable by keyboard from the contact graph
- [ ] `onFocus` parity on the contact graph
- [ ] One `h1` per page, fix the `h2` to `h4` skip
- [ ] Filtered nodes not focusable
- [ ] Skip link
- [ ] Keyboard equivalent for canvas node selection
- [ ] **Manual keyboard pass**, every page, mouse unplugged
- [ ] **400% zoom reflow** check
- [ ] Screen reader pass on home and one case study
- [ ] Forced-colors check
- [ ] axe-core clean on all 9 templates

**Commit:** `fix(a11y): eleven accessibility defects plus manual verification`

---

## PHASE 8 · SEO, performance, launch readiness

- [ ] `noindex` on staging, **impossible to leave on in production**
- [ ] Person schema, no email
- [ ] Per-route titles and descriptions, the design has 9
- [ ] Canonical, Open Graph including an image
- [ ] Favicon, theme colour, manifest
- [ ] The two asset filters verified active
- [ ] Self-hosted fonts, no third-party request
- [ ] LCP under 1.8s, CLS 0.05 or better, INP under 150ms
- [ ] Lighthouse mobile 95+
- [ ] Zero `is_user_logged_in()` branches in templates, for cache correctness
- [ ] RankKernel active, **theme has no code dependency on it**
- [ ] `screenshot.png` 1200x900
- [ ] `readme.txt` current
- [ ] Theme Check 0 errors

**Commit:** `chore(release): v0.2.0 launch candidate`

---

## PHASE 9 · GitHub and ongoing

- [ ] Repo public with a real README
- [ ] RankKernel pinned first on the profile, it outranks four student repos
- [ ] Profile README, none exists
- [ ] Create the actual RankKernel repo, the link currently points at a profile
- [ ] Fix `resume.md` saying Qrologic where everything else says Qrolic
- [ ] Branch protection, CI required
- [ ] `gh` release per version
- [ ] Nightly audit issues triaged
- [ ] Dependabot PRs reviewed

---

## CRITICAL PATH

```
Phase 1 tokens ──> Phase 2 shell ──> Phase 3 case studies ──> Phase 4 pages
       │                                                          │
       └────────────> Phase 5 greenfield <────────────────────────┘
                                   │
                              Phase 6 components
                                   │
                              Phase 7 a11y
                                   │
                              Phase 8 launch
```

**Phase 1 blocks everything.** Every page reads those tokens, so a palette
correction after Phase 3 means rechecking every page.

**Phase 5 is the schedule risk** because none of it is a conversion.

---

## OPEN, NOT BLOCKING

- [ ] Real email, needed for the résumé filename and Person schema. Placeholder
      until then, and the form is the only contact path anyway
- [ ] Phone number. Not published on the site
- [ ] LinkedIn URL trailing-dash discrepancy to verify
- [ ] Real RankKernel repository URL
- [ ] `Distance Based Dynamic Pricing` title. Client pricing logic, not a
      service, but the wording could misread commercially to a skimming
      recruiter. Not on the homepage
