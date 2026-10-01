# DESIGN SPEC
### Extracted from `personal-porfolio-website` (React prototype)
### Source of truth: the design, not the stack. The React implementation is disposable.

---

## 0. WHAT THIS DOCUMENT IS

A complete inventory of the **visual design language** so it can be rebuilt as
a WordPress block theme without looking at the React source again.

The React prototype is a rendering of this design, not the design itself. Every
value below is what the design *is*, independent of how it was drawn.

---

## 1. THE CORE IDEA

**Editorial-technical.** A dark, near-black reading surface with a fine
architectural grid, a single warm amber accent used sparingly as a signal, a
monospace voice for machine-readable metadata, and a geometric display face for
headlines. The reference points are technical documentation and printed
specification sheets, not SaaS marketing pages.

The design has one motion idea: **things fade and slide up once as they enter
the viewport.** Nothing loops, nothing pulses, nothing bobs. There are no
perpetual ambient animations anywhere in the design.

---

## 2. COLOUR

### 2.1 The palette (complete, 13 values)

| Hex | Role | Surface |
|---|---|---|
| `#0A0A0B` | Page ground. Also the text colour on light surfaces. | dark |
| `#000000` | Footer only. Button text on amber. Print text. | dark |
| `#FFFDF4` | Primary text on dark. Selection background. | dark |
| `#FACC15` | **The accent.** Amber. | both |
| `#FDE047` | Amber hover state. | both |
| `#D8D5CA` | Warm grey. Secondary body text. | dark |
| `#77746C` | Muted grey. Tertiary and metadata text. | dark |
| `#111113` | Elevated dark surface. Cards, mobile channel tiles. | dark |
| `#171719` | Secondary button surface. Icon hover. | dark |
| `#262626` | Dark hairline border. The default divider. | dark |
| `#343434` | Lighter border. Hover borders. | dark |
| `#F8F7F0` | Warm off-white. Light editorial section ground. | light |
| `#FFFFFF` | Print only. | light |

All values are uppercase 6-digit hex. The design uses **no** oklch, hsl,
`color-mix()`, or runtime colour variables. Every colour is inlined as an
arbitrary value at its point of use.

### 2.2 The amber is load-bearing

`#FACC15` does four jobs, which is why it can never be used decoratively:

1. The one square bullet in every section eyebrow
2. Active navigation underline and active nav text
3. Primary CTA fill (with `#000000` text)
4. Focus ring on dark surfaces
5. Metadata identifiers in monospace
6. Subtitle text in inner page heroes

Everything else is grey. If amber appears somewhere it is because the design is
pointing at something.

### 2.3 CRITICAL: amber fails contrast on light surfaces

`#FACC15` on `#F8F7F0` is roughly 1.5:1 and on `#FFFDF4` roughly 1.9:1. Both fail
WCAG AA badly.

The design already handles this, which is good practice worth preserving: on
light editorial sections the focus ring **switches to `#0A0A0B`**, and text
switches to `#0A0A0B`. Amber is never used for text on a light surface.

**Any accent-coloured text added on a light section must use a darkened amber,
not `#FACC15`.** A darkened value is not yet defined in the source, so it must
be created and contrast-verified before use. Do not assume `#FACC15` is safe
just because it is the brand colour.

### 2.4 Text contrast pairs that must hold

| Foreground | Background | Ratio | Verdict |
|---|---|---|---|
| `#FFFDF4` | `#0A0A0B` | ~18:1 | pass |
| `#D8D5CA` | `#0A0A0B` | ~12:1 | pass |
| `#77746C` | `#0A0A0B` | ~4.6:1 | pass, AA for body |
| `#FACC15` | `#0A0A0B` | ~13:1 | pass |
| `#0A0A0B` | `#F8F7F0` | ~18:1 | pass |
| `#0A0A0B` | `#FFFDF4` | ~19:1 | pass |
| **`#FACC15`** | **`#F8F7F0`** | **~1.5:1** | **fail, never use** |
| **`#FACC15`** | **`#FFFDF4`** | **~1.9:1** | **fail, never use** |

---

## 3. THE GRID AND TEXTURE BACKGROPS

The design has **four distinct background textures**, each carrying a different
meaning. This is a core part of the visual identity, not decoration.

### 3.1 `dark-grid` (the default page surface)

```css
background-color: #0A0A0B;
background-image:
  linear-gradient(to right,  rgba(255,253,244,0.035) 1px, transparent 1px),
  linear-gradient(to bottom, rgba(255,253,244,0.035) 1px, transparent 1px);
background-size: 48px 48px;
```

A 48px square grid in cream at 3.5% opacity. Reads as graph paper. Used on the
homepage hero and every inner page hero.

### 3.2 `dark-subgrid`

```css
background-image: radial-gradient(circle at 1px 1px, rgba(250,204,21,0.14) 1px, transparent 0);
background-size: 32px 32px;
```

A 32px dotted grid in amber at 14% opacity. **No background colour declared**, so
it assumes a dark parent. Used behind the skills ecosystem. The amber tint is
what distinguishes it from `dark-grid` at a glance.

### 3.3 `cream-editorial`

```css
background-color: #FFFDF4;
color: #0A0A0B;
background-image: linear-gradient(to right, rgba(10,10,11,0.04) 1px, transparent 1px);
background-size: 96px 96px;
```

Warm off-white with 96px vertical hairlines. Vertical only, no horizontal. Used
for the light rhythm section that breaks up the dark run.

### 3.4 `warm-editorial`

```css
background-color: #F8F7F0;
color: #0A0A0B;
background-image: linear-gradient(to bottom, rgba(10,10,11,0.035) 1px, transparent 1px);
background-size: 64px 64px;
```

Warmer, slightly deeper off-white with 64px horizontal lines. The mirror of
`cream-editorial`. Distinguishable from it by both the warmer tone and the
opposite line orientation.

**The grid sizes are load-bearing: 48, 32, 96, 64. They are not arbitrary and
should not be unified.**

---

## 4. TYPOGRAPHY

### 4.1 The three voices

| Voice | Family | Applied to | Loaded weights |
|---|---|---|---|
| Display | **Syne** | all `h1`, `h2`, `h3`, and any display class | 600, 700, 800 |
| Body | **Plus Jakarta Sans** | `body`, all prose | 400, 500, 600, 700 |
| Machine | **IBM Plex Mono** | metadata, eyebrows, breadcrumb, timestamps, footer legal line | 400, 500, 600 normal, 400 italic |

**The mono voice is what makes the design read as technical.** It carries every
piece of machine-readable information: the section identifiers
(`404 / ROUTE NOT RESOLVED`), breadcrumbs, metadata dot lists, the footer legal
line. Removing it would make the site look like a generic dark portfolio.

All three are loaded from Google Fonts CSS2 with a preconnect to
`fonts.googleapis.com` and `fonts.gstatic.com`. No local `font-face`, no preload
of individual woff2 files, no `font-display` control.

### 4.2 The type scale

Tailwind defaults, no overrides, no `clamp()` anywhere:

| Step | Value |
|---|---|
| `xs` | `0.75rem` (12px) |
| `sm` | `0.875rem` (14px) |
| `base` | `1rem` (16px) |
| `lg` | `1.125rem` (18px) |
| `xl` | `1.25rem` (20px) |
| `2xl` | `1.5rem` (24px) |
| `3xl` | `1.875rem` (30px) |
| `4xl` | `2.25rem` (36px) |
| `5xl` | `3rem` (48px) |
| `6xl` | `3.75rem` (60px) |

Line height overrides in use: `1.08` on the hero `h1` (tight, display), `relaxed`
(1.625) on body descriptions, `1.6` base.

### 4.3 Typography details that must be preserved

- **`text-wrap: balance` on all headings.** Prevents orphaned words in the
  large display sizes. Genuinely improves the design, easy to lose.
- **`font-variant-numeric: tabular-nums` on all mono.** Makes timestamps and
  numeric metadata align in a column. The mono usage depends on this.
- **The hero `h1` uses `leading-[1.08]`,** much tighter than the 1.6 base. This
  single value is most of the display type's character.

### 4.4 Two heading treatments

**Hero (homepage and inner pages):**
`Syne` extrabold 800, `tracking-tight`, responsive `4xl` to `sm:5xl` to `md:6xl`,
`leading-[1.08]`, colour `#FFFDF4`.

**Inner page subtitle:**
`Syne` semibold 600, `xl` to `sm:2xl`, colour `#FACC15`, sits directly under the
`h1` with a `space-y-3` gap.

---

## 5. SPACING, RADIUS, ELEVATION

### 5.1 Radius: **zero, everywhere**

There is not a single rounded corner in the entire design. No `rounded*` class,
no radius variable. Corners are square, always.

**This is a defining characteristic.** Adding a 4px radius anywhere would make
it look like a different site. It also simplifies implementation.

### 5.2 Shadow: **zero**

No custom shadows, no elevation glow. Depth is communicated only by the surface
step from `#0A0A0B` to `#111113` to `#171719`, and by the `#262626` hairline.

### 5.3 The container

```
max-width: 1440px
padding:    1.5rem (24px) at base, 2.5rem (40px) at md and up
```

**1440px is wider than the stock 1280px breakpoint scale.** The design is
deliberately wide. At 1536px and up it still centres at 1440px.

### 5.4 Vertical rhythm

Section padding: `py-16` (64px) at base, `md:py-24` (96px) at md and up.
First section after the fixed header gets `pt-28` (112px) to clear it.
Header height: `h-16` (64px).

### 5.5 Breakpoints

Stock: `sm` 640px, `md` 768px, `lg` 1024px, `xl` 1280px, `2xl` 1536px.
Only `sm`, `md`, and `lg` are actually used.

---

## 6. THE SQUARE BULLET MOTIF

A recurring, unmissable element. An **8x8px solid square** in amber, rendered as
a plain span with `aria-hidden="true"`, not a dot, not an icon, not a circle:

```html
<span class="w-2 h-2 bg-[#FACC15]" aria-hidden="true"></span>
```

It opens every section eyebrow. It appears in the inner page hero, the not-found
view, and elsewhere. This is the design's smallest repeating signature and
should be treated as a required motif wherever an eyebrow appears.

The separator between breadcrumb items is a plain `/` character in muted grey,
also `aria-hidden`. The separator between metadata items is a `·` middot, also
`aria-hidden`.

---

## 7. LAYOUT PATTERNS

### 7.1 The shell

```
[fixed header, 64px, transparent until scrollY > 24]
[main content]
[footer, pure #000000]
```

The header is fixed. Content clears it with `pt-28` on the first section. The
header has two states:

- **At rest:** `bg-transparent`, bottom border transparent
- **Scrolled (past 24px) or menu open:** `bg-[#0A0A0B]/92` with `backdrop-blur-md`
  and a `#262626` bottom border

### 7.2 The 12-column grid

The dominant layout primitive throughout. Key uses:

| Use | Columns |
|---|---|
| Inner page hero, text | `lg:col-span-7` |
| Inner page hero, right panel | `lg:col-span-5` |
| Inner page hero, no right panel | `lg:col-span-9` |
| Footer, identity | `md:col-span-5` |
| Footer, Explore | `md:col-span-2` |
| Footer, Development | `md:col-span-3` |
| Footer, Connect | `md:col-span-2` |

The footer's 5/2/3/2 split is asymmetric on purpose and should not be
"balanced" to 3/3/3/3.

### 7.3 Hairline rules as structure

`#262626` at various opacities is used as the sole visual separator throughout:
footer top border, footer internal rule, breadcrumb row bottom border, card
borders, grid dividers. There is no alternative separator treatment.

---

## 8. BUTTONS AND LINKS

### 8.1 Primary CTA

- Fill `#FACC15`, text `#000000`
- Hover fill `#FDE047`
- Square corners
- Padding roughly `0.75rem` vertical, `1.5rem` horizontal
- Font weight 600

### 8.2 Secondary CTA

- Fill `#171719`, text `#FFFDF4`
- Border `#343434`
- Hover border lightens

### 8.3 Navigation links

All nav links are `py-1` with a `2px` bottom border that is `border-transparent`
at rest and `#FACC15` when active. Hover lightens the border to `#343434`.
Active link text is `#FFFDF4`, inactive is `#D8D5CA`.

The Resume nav item is special: it is **always amber** (`#FACC15`) whether
active or not, and it opens the résumé modal rather than navigating.

### 8.4 Focus rings

```css
*:focus-visible { outline: 2px solid #FACC15; outline-offset: 3px; }
.bg-cream-editorial *:focus-visible,
.bg-warm-editorial *:focus-visible { outline: 2px solid #0A0A0B; outline-offset: 3px; }
```

2px solid, 3px offset. Amber on dark, near-black on light. The light-surface
override is a genuine WCAG requirement, not a stylistic choice.

---

## 9. MOTION

### 9.1 The only motion idea

**Scroll-triggered reveal: fade in and translate up, once, on entering the
viewport.** Elements do not re-animate. There are no looping, pulsing, bobbing,
or perpetual ambient animations in the design.

### 9.2 Durations

| Context | Duration | Easing |
|---|---|---|
| Header colour change on scroll | 200ms | default |
| Project card hover | 200ms | default |
| Project card transform | 200ms | default |
| Scroll reveal | see reveal component | default |

Default easing only (`cubic-bezier(0.4, 0, 0.2, 1)`). No custom bezier curves
anywhere. No `@keyframes` in the base stylesheet.

### 9.3 Reduced motion is already handled

```css
animation-duration: 0.01ms !important;
animation-iteration-count: 1 !important;
transition-duration: 0.01ms !important;
scroll-behavior: auto !important;
```

**This must survive the port.** It is present in the design source and is
load-bearing for vestibular safety, not an afterthought.

### 9.4 Smooth scrolling

`scroll-behavior: smooth` on the root element, plus a programmatic
`window.scrollTo({ top: 0, behavior: 'smooth' })` on navigation.

---

## 10. ICONOGRAPHY

The Lucide icon set, stroke style, `currentColor` inheritance, sized by width and
height classes. Icons in use across the shell: `ArrowLeft`, `ArrowRight`, `Mail`,
`Menu`, `X`, `Github`, `Linkedin`, `FileText`, `ArrowUpRight`.

Sizes in use: 14px (back link), 16px (default), 20px (mobile toggle, file icon).

**The icon set is not load-bearing for the design's character.** Roughly nine
icons. Inlining the SVG paths directly into block markup is preferable to
shipping an icon library, and loses nothing visually.

---

## 11. PAGE INVENTORY

Eight routes plus a 404 fallback. Every path is trailing-slash.

| Path | Page | Notes |
|---|---|---|
| `/` | Home | The 8-section rhythm |
| `/rankkernel/` | RankKernel | 555 lines in source, the deepest page |
| `/projects/` | Projects archive | Filterable grid |
| `/projects/{slug}/` | Case study | 399 lines, the second deepest |
| `/about/` | About | 440 lines |
| `/resume/` | Résumé | 211 lines |
| `/contact/` | Contact | 164 lines, form with intent dropdown |
| (unmatched) | 404 | Inline fallback, no real URL |

**No Writing or Blog route exists.** Navigation is Home, RankKernel, Projects,
About, Resume, Contact. The theme must be architected to support a future blog
without adding it to navigation now.

### 11.1 Per-route SEO titles

Each route sets its own `document.title` and meta description. The titles are:

- Home: `Maulik Bhalodiya | WordPress Developer and PHP Engineer`
- RankKernel: `RankKernel | Open Source SEO and Schema Engine for WordPress`
- Projects: `WordPress and PHP Development Work | Maulik Bhalodiya`
- Case study: `{project title} | Maulik Bhalodiya`
- About: `About Maulik Bhalodiya | WordPress and PHP Developer`
- Contact: `Contact Maulik Bhalodiya | WordPress Developer`
- Résumé: `Maulik Bhalodiya | WordPress Developer Resume`
- 404: `Page Not Found | Maulik Bhalodiya`

**There is no `404.html` route in the design.** A WordPress block theme must
provide one as a real file. The design's inline fallback has an eyebrow reading
`404 / ROUTE NOT RESOLVED`, which is good and should be preserved.

---

## 12. FOOTER

Pure `#000000` background, `#FFFDF4` text, `#262626` top border, hidden on print.

Four-column 12-grid: identity (5), Explore (2), Development (3), Connect (2).
Above the copyright row, separated by a `#262626` rule.

**Group headings are amber.** The bottom row is left copyright in `#77746C` and
right a mono technical line: `WordPress Block Theme Ready Architecture ·
Structured Data Prototype`.

Copyright year is generated from the current date, not hardcoded.

---

## 13. STRUCTURED DATA

Person schema is inlined in the design as JSON-LD:

- `name`: Maulik Bhalodiya
- `jobTitle`: WordPress Developer and PHP Engineer
- `alumniOf`: Ganpat University
- `worksFor`: Qrolic Technologies
- `knowsAbout`: 10 entries (WordPress Engineering, PHP Development, Custom
  WordPress Plugin Development, REST API Integration, Payment Integration,
  WordPress Security, Gutenberg Block Development, WooCommerce, MySQL, HMAC
  SHA 256)

This should become a proper schema block rather than hand-written JSON-LD.

---

## 14. COMPLIANCE DEFECTS FOUND IN THE DESIGN SOURCE

These must be fixed during the port. Listed with the reason each is a defect.

### 14.1 Raw email address, published in four places

`PROFILE_DATA.links` contains both `email: 'mailto:...'` and a bare
`emailAddress: '...'`. The address surfaces in:

1. The header icon row (mail icon, `aria-label` "Send an email to Maulik Bhalodiya")
2. The mobile menu Direct Channels grid (Email tile)
3. The footer Connect column (Email link)
4. Anywhere `PROFILE_DATA.links` is spread into a page

**Research established this address is spam-listed within 48 hours of
publication.** Every mailto must become the contact form. This also removes the
header's `aria-label` "Send an email" affordance problem, since a mail icon
implies a mail client.

### 14.2 No skip link

There is no skip-to-content link. `main#main-content` exists, so the target is
already correct and this is a one-line addition. Block themes get a skip link
into `<main>` from core in some configurations, but this must be verified in the
rendered output rather than assumed.

### 14.3 Missing head tags

No `og:image`, no `og:url`, no canonical, no `theme-color`, no favicon of any
kind, no manifest, no apple-touch-icon.

### 14.4 Font loading is the biggest performance liability

Three Google Fonts families with 11 loaded weights from a third-party origin, no
`font-display` control, no preload, no subset. This is a render-blocking
third-party stylesheet on the critical path of every page.

**This directly conflicts with the CLS budget of 0.05.** Without
metric-matched fallback `@font-face` declarations, a font swap will shift text
and blow the budget. Metric overrides are required, with multiple fallbacks
because Arial does not exist on Android.

### 14.5 Tailwind classNames used as the token system

Every colour, spacing value and radius is an arbitrary value inlined at point of
use rather than defined once. There is no `@theme` block, so there is no single
source of truth. The palette above had to be reverse-engineered from component
files.

This is a tooling artefact, not a design flaw. In the block theme, `theme.json`
becomes the single authoritative source and every arbitrary hex resolves to a
`--wp--preset--*` custom property.

---

## 15. WHAT THE DESIGN ALREADY DOES WELL

Worth stating, because these are easy to lose in a port and each is a real
accessibility or quality property:

- **Reduced motion is handled** with a proper `!important` override block
- **Focus rings are visible, offset, and switch colour on light surfaces**
- **Amber is never used as text on a light background**, correctly avoiding the
  contrast failure
- **Tabular numerals on all mono** metadata
- **Text balancing on headings**
- **One motion idea only**, no ambient loop
- **Zero border radius and zero shadows**, a coherent visual system rather than
  incidental styling
- **The mono voice carries all machine metadata**, giving the site a technical
  voice without any hacker aesthetic
- **`aria-hidden` on every decorative square and separator**
- **No email, no phone, no social-follow CTAs** in the design's voice
- **Print styles** invert to white background and black text

---

## 16. OPEN ITEMS FOR THE NEXT COMMAND

1. **The 8-section homepage rhythm**, exact order and copy. Analysis in progress.
2. **Full case study content**, verbatim, for all projects. Analysis in progress.
3. **Skills ecosystem** structure, node data, and relationship model. In progress.
4. **RankKernel page** structure and the status model. In progress.
5. **Contact form** fields, intent dropdown options, and validation. In progress.
6. **Canvas component** rendering technique and port verdict. In progress.
7. **A darkened amber value** for accent text on light surfaces, not yet defined.
8. **A self-hosted font subset** replacing Google Fonts, not yet chosen.
9. **`404.html`** as a real template, not currently in the design.
10. **Whether `/rankkernel/` is in scope now** or deferred. The user previously
    said portfolio first, RankKernel later. The design has it as a full page and
    a nav item, so the design and the earlier instruction now disagree.
