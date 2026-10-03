# PLAN
### `maulik-portfolio` WordPress block theme · what we are building and why
### The approved design at `maulikbhalodiya/personal-porfolio-website` is final. This is how it becomes WordPress.

---

## 1. WHAT THIS IS

A native WordPress block theme that is a faithful conversion of the approved
React design prototype. The prototype is disposable. The design is not.

The site is a recruiter-facing portfolio for a WordPress and PHP developer. It
presents engineering work with verifiable specificity and never claims anything
unverifiable. RankKernel, his own open source SEO plugin, is the SEO engine for
the site itself, so the portfolio is its own case study.

---

## 2. DECISIONS NOW LOCKED

| Decision | Value |
|---|---|
| Platform | WordPress block theme, no framework |
| Path | Single theme, no wp.org submission, built to review standards anyway |
| Slug | `maulik-portfolio` |
| Content model | Nested blocks in post content, **no custom post type** |
| SEO | **RankKernel, his own plugin. Not Rank Math, not Yoast.** |
| Rank Math case study | **Removed entirely.** The site does not use it and never did as his product. |
| Email | Placeholder. No real address published. |
| `/rankkernel/` | **Ships.** It is the site's own SEO engine. That is the story. |
| Timeline dates | **Added.** Verified from resume. |
| Git | Repo created, versioned from first commit, tagged releases |

---

## 3. CONTENT MODEL, AND WHY NO CPT

Research established that WordPress.org forbids custom post types, custom
taxonomies and custom blocks that read project data in themes. We are not
submitting, but we build to that standard because it produces better
architecture. The boundary is presentation: presentational blocks such as a card,
a badge or a flow belong in the theme and are allowed here, while a block whose
`render.php` reads posts, meta or terms is plugin territory. See
[`ARCHITECTURE.md`](ARCHITECTURE.md) for the full test.

The designer's own blueprint in the data file proposed a `project` CPT with four
custom blocks. That is a plugin architecture and it would also have broken on
first use, because its field list omits `architectureFlows` which the
architecture diagram renders.

**What we do instead:** every project is a WordPress page. Its case study is
nested blocks. The architecture diagram is a nested-block structure with real
buttons. The skills ecosystem is a nested group. Content stays fully editable in
the Site Editor by anyone, forever, with no plugin dependency.

The cost is that we cannot query projects by taxonomy. We do not need to. There
are eight. Server-side filter links on the archive page handle filtering, which
research confirms is strictly better for Core Web Vitals than a client-side
filter and works without JavaScript.

---

## 4. PAGE INVENTORY

Nine templates, matching the design's routes exactly.

| Template | Route | Content |
|---|---|---|
| `front-page.html` | `/` | The 8-section homepage |
| `page-projects.html` | `/projects/` | Archive, server-side filters |
| (child of above) | `/projects/{slug}/` | Case study, 12 sections |
| `page-about.html` | `/about/` | Engineering profile |
| `page-contact.html` | `/contact/` | Contact form |
| `page-resume.html` | `/resume/` | Résumé viewer |
| `page-rankkernel.html` | `/rankkernel/` | RankKernel architecture |
| `404.html` | any | Not found |
| `home.html` + `single.html` | `/blog/{slug}/` | Future posts, no nav link, no menu item |

**Case study sections, the 12 the design uses:**

Overview, Problem, My Role, Technical Approach, Architecture, Engineering
Decisions, Implementation, Security, Verification, Technologies,
Confidentiality, Related Work.

There is deliberately **no Impact section and no metrics**, because outcomes
require numbers and inventing numbers is forbidden. The design already made this
choice correctly. We keep it.

**Section 08 Security has no source field.** It derives from the validation notes
on each architecture flow, so security claims cannot drift from the architecture
they describe. That derivation is preserved.

---

## 5. RESUME RECONCILIATION

The resume adds verified detail the design prototype omits. All of it is real
work and belongs in the case studies. Enriching the case studies with it makes
them substantially stronger, because a named gateway and a first-of-kind
integration are far more convincing than an abstraction.

### 5.1 Detail to add

**Cross-Domain Payment Architecture.** Gateway names: Checkout.com,
ConvesioPay, TailoredPay. Three Gravity Forms payment add-ons built where none
existed, specifically Checkout.com webhook support plus a modern components UI
rather than legacy frames, the first Gravity Forms integration for ConvesioPay
built from API documentation, and the first WordPress integration of any kind for
TailoredPay. Three-D Secure flows, synchronous payment handling, signed
callbacks, duplicate checks. The business constraint was enabling payments on
domains not approved to hold them, with no redirect and no trust loss.

**Employee Application Management.** Ten-plus AJAX filter parameters,
approve and reject with automated Brevo notification, internal notes, activity
logging, mPDF generation, one codebase serving both Forminator and Gravity Forms.

**Secure Data Encryption.** Encryption key stored outside the WordPress root.
LatePoint booking notes, messages and custom fields.

**Identity Document OCR.** Passport and identity documents specifically.
Server-side processing with asynchronous polling.

**Object Storage.** Hetzner, S3-compatible, scheduled Gravity Forms file
migration on WP-Cron.

**Distance Pricing.** Google Maps Distance Matrix API, per-mile billing beyond a
free allowance, Stripe Payment Intents, admin approval before charge.

**Brevo Automation.** Multi-language template selection, PDF attachments,
event-driven attribute changes on payment status and application stage.

### 5.2 Client names stay out

The resume names two clients. The site names none, and the design's
confidentiality framing is well judged. **Client names do not go on the site**,
including the two in the resume, because:

- The design already anonymises all eight projects, consistently
- Naming one client while anonymising seven invites questions about the others
- Anonymity is framed as professional judgement, not apology, which is correct

The résumé document itself is different. It is a document he sends
deliberately to a specific employer, so it may carry a client reference line
that the public site does not. That is a reasonable distinction and worth making
explicitly.

### 5.3 Two claims to strip

**HIPAA-aligned.** The resume says the encryption work is HIPAA-aligned. This
must not appear on the site. It is a regulatory compliance claim, we cannot
verify it, and claiming it invites exactly the scrutiny that a portfolio
citation should not attract. The verifiable claim is field-level authenticated
encryption with external key material, which is strong on its own.

**1.5 years.** Both resumes undercount. Internship January to June 2025 plus
full-time from July 2025 is roughly twenty-one months. The site's About and
Résumé pages will state the verified dates rather than a rounded total, which is
both more honest and more impressive than either version.

### 5.4 Data corrections

| Issue | Correction |
|---|---|
| Resume 1 says `Qrologic Technologies` | **Qrolic.** Resume 2 and every other source use Qrolic. Standardise. |
| Résumé contact line shows `github.com/XXXXXXXX` | Placeholder. Use the real profile URL. |
| LinkedIn shown as `linkedin.com/in/maulik-bhalodiya-` with trailing dash | **Superseded 2026-10-03.** This row previously recorded "The real URL has no trailing dash. Verify before use." The owner has since stated the opposite: the trailing-dash form is the real profile, and the trailing-dash-free URL resolves to a different person also named Maulik Bhalodiya. The trailing-dash form is now in use. Not machine-verifiable: LinkedIn returns a bot challenge rather than the profile page, so the owner's word is the authority here. |
| `+91 XXXXX XXXXX` | Placeholder. Phone is not published on the site at all. |

---

## 6. TECHNICAL ARCHITECTURE

### 6.1 Files

```
maulik-portfolio/
├── style.css                    required block theme marker
├── theme.json                   v3, 13-value dark palette, 3 font families
├── functions.php                thin loader
├── readme.txt  screenshot.png
├── templates/                   9 templates, structure and hierarchy only
├── parts/                       header.html, footer.html, flat, no PHP
├── content/pages/               source of truth for post_content, the DB is generated
├── patterns/                    reusable compositions and chrome i18n
├── styles/                      style variations as complete design systems
├── inc/                         setup, assets, seo, blocks, resume, contact
├── assets/css/                  COMMITTED
├── assets/scss/                 source, @use only, never enqueued
├── assets/js/                   hero canvas, modal, diagrams, reveal
├── assets/fonts/                self-hosted woff2 subsets
├── languages/                   maulik-portfolio.pot
└── scripts/generate-scss-tokens.mjs
```

**Never create `block-templates/` or `block-template-parts/`.** Core switches to
legacy folders if either exists, which silently breaks everything.

**Translatable strings in templates and template parts live in `patterns/` and in
`post_content`, never in a `.html` file.** Block theme templates and parts are
`.html` and are parsed rather than executed, so they cannot call `esc_html__()`
and a literal string in one can never be translated. `patterns/*.php` and the
`post_content` of a page are the two homes that work, because both run PHP.
This is the most common block theme porting mistake and it is invisible in a
screenshot. See [`ARCHITECTURE.md`](ARCHITECTURE.md) for where each kind of
string belongs.

### 6.2 Two lines that decide performance

```php
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_filter( 'should_load_block_assets_on_demand', '__return_true' );
```

Without these, WordPress ships a 100KB+ block-library stylesheet on every page
including pages using fifteen of ninety blocks.

### 6.3 theme.json v3 traps

- `version: 3` **flips two defaults to true** that were false in v2. Must set
  `defaultFontSizes: false` and `defaultSpacingSizes: false` explicitly, or
  core's light-mode defaults reappear beside ours.
- `color.defaultPalette: false` and `defaultGradients: false`. Core's palette is
  light mode and would break the dark theme.
- `useRootPaddingAwareAlignments: true` must be paired with an explicit
  `styles.spacing.padding` object or it does nothing.
- **Append palette entries at the end. Never reorder.** Translation uses
  positional JSON key paths as context, so reordering silently breaks existing
  translations.

### 6.4 Design tokens

Palette, thirteen values. `#0A0A0B` ground, `#000000` footer, `#FFFDF4` text on
dark, `#FACC15` accent, `#FDE047` accent hover, `#D8D5CA` secondary text,
`#77746C` muted, `#111113` and `#171719` elevated surfaces, `#262626` and
`#343434` borders, `#F8F7F0` light editorial ground, `#FFFFFF` print only.

**The two-yellow fix is mandatory.** `#FACC15` on `#F8F7F0` is about 1.5:1 and
fails AA outright. The darkened light-surface accent is **`#8A6A00`**, already
verified in the earlier prototype. `theme.json` declares both.

Typography, three voices. **Syne** for display at 600, 700, 800. **Plus Jakarta
Sans** for body at 400 to 700. **IBM Plex Mono** for all machine-readable
metadata, with tabular numerals. The mono voice is what makes the site read as
technical rather than generic, and it must survive.

**Zero border radius. Zero shadows.** Everywhere. That is the design's spine,
not an omission. Adding any radius makes it look like a different site.

**Four grid textures at four different sizes**, 48, 32, 96 and 64 pixels, two
dark and two light. They carry different meaning and must not be unified.

### 6.5 Fonts must be self-hosted

The prototype loads three Google Fonts families with eleven weights from a third
party, no `font-display` control and no preload. That will blow the 0.05 CLS
budget on its own.

We self-host woff2, Latin subset, two weights per family maximum, and declare
**metric-matched fallback faces** using `size-adjust`, `ascent-override`,
`descent-override` and `line-gap-override`. Multiple fallbacks are mandatory
because Arial does not exist on Android and needs its own override set.

### 6.6 RankKernel integration

RankKernel is the SEO plugin for this site. **The theme must not depend on it
in code.** No `class_exists` checks, no conditional rendering, no coupling. If
RankKernel were deactivated the site would lose SEO features and nothing else.

The theme references it as data, in a case study page and one homepage section.
That is the whole integration surface.

---

## 7. COMPONENT CONVERSIONS

| Design component | Becomes | Difficulty |
|---|---|---|
| Hero ecosystem canvas | Vanilla JS view script | **Real work.** Pause off-screen, pause on hidden tab, wait for fonts before first paint |
| Skills ecosystem SVG | Nested block markup | Markup. HTML controls are the accessible layer, SVG is decorative |
| RankKernel architecture SVG | Nested block markup | Markup |
| Contact signal SVG | Nested block markup | Markup |
| Project card | Nested block markup | Markup. Remove `focus:outline-none` |
| Architecture diagram | Nested blocks with buttons | Markup. Key flows by node id, not label string |
| Résumé modal | Vanilla JS | Markup plus a focus trap and `inert` |
| **Contact form** | **Built from nothing** | **New work** |
| **Scroll reveals** | **Built from nothing** | **New work** |
| **Résumé PDF generation** | **Built from nothing** | **New work** |

Four of five interactive components are static SVG or plain DOM, which makes them
markup conversions rather than engineering. The three greenfield items are the
schedule risk, and none of them is a port.

**The contact form does not exist in the design in any form.** No form element,
no intent dropdown, no submit handler, no network request. It is built from zero
using `core/form`, which WordPress 6.7 ships natively.

**The prototype has no scroll-reveal animation anywhere.** The `motion` package
is in its dependencies and imported by no file. Scroll reveals are new work and
are the single highest-value addition available, because the design's restrained
aesthetic currently has no entrance motion on eight long sections.

---

## 8. COMPLIANCE

Five rules, each with enforcement rather than intention.

| Rule | Enforcement | Design state |
|---|---|---|
| Never position as freelance | Copy review | Clean. Section 08 is "Looking for a WordPress or PHP developer?" which is job-seeking |
| Never state the employment constraint | Copy review | Clean. Never mentioned anywhere |
| No published email | Placeholder, contact form only | Nine mailto and seven plaintext occurrences to remove |
| No skill percentages, stars, meters | No such field exists | Clean. Skills are `{name, context}`, never a level |
| No em or en dashes | CI grep | Clean. Zero in the design |

**Three additional standing rules:**

RankKernel stays independent. Presented as data, never as a dependency.

No Writing or Blog route yet. Templates support future posts, nav has no link.

No invented anything. Metrics, percentages, testimonials, client names, client
domains and unverified capabilities are structurally absent from the data model.
There is nothing to strip because there was never anything to add.

**Rank Math is removed.** The site does not use it, he did not build it, and
carrying a case study for a competitor's plugin on a site that runs its own
rival plugin was a liability. The design's own text admitted the confusion, which
is exactly why it was never right to publish.

---

## 9. PERFORMANCE BUDGET

| Metric | Target | Requirement |
|---|---|---|
| LCP | under 1.8s | 2.5s |
| INP | under 150ms | 200ms |
| CLS | **0.05 or better** | 0.1 |
| TTFB | under 400ms | 0.8s |
| TBT | under 100ms | heaviest Lighthouse weight |
| Lighthouse mobile | 95+ | |

Budgets: 150KB JavaScript, 50KB CSS, two web fonts maximum, zero jQuery.

**Zero `is_user_logged_in()` branches in templates.** Full page caching cannot
work for authenticated users, and a portfolio has no reason to produce
per-user front-end output. This is a design decision with a performance
consequence, and it is free.

---

## 10. ACCESSIBILITY

The design already gets some things right that must survive: a correct
`:focus-visible` ring that switches colour on light surfaces, `aria-hidden` on
every decorative square and separator, a correctly implemented dialog pattern
with focus restore, and status encoded by dash pattern plus text rather than
colour alone.

**Eleven defects to fix:**

1. `role="img"` on three SVGs that contain focusable children collapses the
   accessibility tree and makes them unreachable by screen reader
2. `role="tablist"` with no `role="tabpanel"` anywhere, three times
3. The RankKernel status filter uses tab semantics when it is a filter, giving 25
   or more tab stops with no arrow-key navigation
4. `focus:outline-none` on the project card title link deletes its focus ring
5. The résumé is unreachable by keyboard from the contact graph
6. Contact graph has no `onFocus` handler, so keyboard focus updates nothing
7. Two `h1` elements on the résumé page
8. `h2` to `h4` skip on the RankKernel page
9. Filtered architecture nodes stay focusable with no announcement
10. No skip link, though the `main` target exists
11. Hero canvas has no keyboard equivalent for node selection

Automated tooling catches roughly a third to two-fifths of real issues. A manual
keyboard pass and a 400 percent zoom reflow check are required and are not
optional extras.

---

## 11. WHAT MUST NOT BE LOST

Each of these is easy to treat as incidental during a rebuild and each is
load-bearing.

- **Zero radius, zero shadows.** The visual spine.
- **Four grid textures at four sizes.** Do not unify.
- **The 8x8px amber square** opening every section eyebrow.
- **Monospace carrying all machine metadata.**
- **One motion idea only.** Fade and rise once. No loops, no pulses, no ambient
  loop.
- **Reduced motion handled properly**, including the JS `matchMedia` check that
  actually stops the canvas loop, since CSS cannot.
- **Focus ring switching colour on light surfaces.** A real WCAG requirement.
- **Amber never used as text on a light background.** Already correct.
- **Skills written as use-case, never as level.** Forty-one instances. There is
  no field for ratings and there should never be one.
- **No metrics, no client names, no outcome claims.** Structural, not vigilance.
- **Zero runtime network requests.** The design calls nothing.
- **Middot as separator**, consistently `aria-hidden` where decorative.
- **The Security section derived from flow validation notes**, so it cannot
  drift from the architecture.
