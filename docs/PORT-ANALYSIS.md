# PORT ANALYSIS
### Interactive components, page composition, accessibility, compliance
### Focus is the DESIGN. The React implementation is irrelevant.

---

## 0. THE THREE FINDINGS THAT CHANGE THE BUILD

### 0.1 There is no contact form. None. It must be built from scratch.

An exhaustive sweep of `src/` for `<form`, `onSubmit`, `<select`, `honeypot`,
`required`, `fetch(`, `XMLHttpRequest`, `/wp-json`, `admin-post.php` returned
**zero matches**.

The contact page is four clickable channel cards plus a static SVG diagram.
Contact is entirely `mailto:`, LinkedIn, GitHub, and the resume modal.

**This is not a port. It is a greenfield build**, and it is the single largest
piece of new work in the project. WordPress 6.5+ ships a native `core/form`
block, which is the right tool and is theme-compatible.

### 0.2 There is no scroll-reveal animation anywhere

`motion` v12.23.24 is in `package.json` but **is not imported by a single file**.
No `IntersectionObserver`, no `whileInView`, no `animate-*` utility, no reveal
class. Grep for `from 'motion'`, `useScroll`, `useInView`, `animate(`: no matches.

The design has **exactly one motion behaviour**: the hero canvas orbit, plus
200ms hover transitions.

Earlier specs assumed scroll-triggered fade-and-slide reveals. **That is new
work, not a port.** It is cheap to build and it is the single highest-value
addition available, because the design's restrained aesthetic currently has no
entrance motion on eight long sections.

### 0.3 The résumé download is client-side string templating

`triggerResumeDownload()` builds a complete standalone HTML résumé as a
**template literal**, wraps it in a `Blob`, and synthesises a download link. No
server, no network.

The generated HTML has inline styles with hardcoded hex and a system font stack,
so **the downloaded file does not match the site design at all**.

Must be rewritten as a PHP-generated file or a stored HTML attachment in
`/wp-content/uploads/`.

---

## 1. THE FIVE COMPONENTS

| Component | Technology | Animation | Port verdict |
|---|---|---|---|
| Hero3DEcosystem | Canvas 2D + hand-rolled 3D math | rAF orbit, never pauses | Near-verbatim, needs engineering |
| TechnicalSkillsEcosystem | Inline SVG, static | none | Markup conversion |
| RankKernelArchitecture | Inline SVG, static | none | Markup conversion |
| ResumeModal | Pure DOM + Blob | none | Fully portable, rewrite download |
| ContactSignalNetwork | Inline SVG, static | none | Markup conversion |

**Four of five are static SVG or DOM.** They are effectively markup
conversions, not engineering. Only the hero requires real decisions.

### 1.1 Hero3DEcosystem: canvas 2D with hand-written 3D

No WebGL, no `three.js`, no CSS `preserve-3d`. A projector function rotates on Y
then X and does a perspective divide by `fov / max(distance + z2, 120)`. About
300 canvas path ops per frame across 14 core vertices, 24 edges, 2 orbital
rings, 8 nodes, 16 mesh edges and 8 signal packets. Cheap in absolute terms.

**Three real problems:**

1. **The loop never pauses.** No `IntersectionObserver`, no
   `visibilitychange`, no off-screen pause. It runs at 60fps whenever the hero
   is in the DOM, even scrolled far out of view. Browsers throttle background
   tabs, but that is browser behaviour, not code.

2. **The effect re-runs on every hover change.** The dependency array includes
   `hoveredNodeId`, so each hover teardown and re-runs the effect. 8 nodes means
   frequent re-entry.

3. **`getBoundingClientRect()` is called every frame**, a forced layout read per
   frame, plus roughly 30 to 40 short-lived array allocations per frame from
   `.map`, spread and `.sort`.

**Reduced motion is genuinely handled.** A `matchMedia` listener sets paused
state, and a global CSS `!important` block exists as backup. Note the CSS block
does **not** stop a canvas rAF loop, so the JS check is what actually works.

**Font ordering hazard:** the canvas paints labels with
`ctx.font = '11px "IBM Plex Mono", monospace'`. If the web font has not loaded
when the first frame paints, labels render in system mono and stay wrong until a
repaint. Needs `document.fonts.ready` before the first render.

**Good:** it already has Pause, Resume and Reset controls with `aria-label`s, and
a node tablist with `aria-selected`.

### 1.2 TechnicalSkillsEcosystem: static SVG, 520x420

7 category nodes on an ellipse, 7 connection lines, 1 inner circle. Click or
Enter/Space selects a category, a panel on the right re-renders.

**Coupling hazard:** node coordinates live in a positional array indexed by
`idx`, so reordering the categories array breaks the graph silently. **Key the
coordinates by category id.**

### 1.3 RankKernelArchitecture: static SVG, 760x480

9 subsystem nodes around a core, 14 dependency edges, 4-state status filter, and
a 9-item directory grid. Status is encoded with **stroke dash patterns**
(solid / dashed / dotted) plus a text label, so it is not colour-only. Good.

**Coupling hazard:** `nodeCoords` is keyed by id, which is better, but an unknown
id returns `undefined` and the code silently renders nothing. A subsystem with
no coordinate vanishes with no warning.

**Two defects:** the status filter label hardcodes the count, `All States (9)`,
which will go stale. And the secondary-edge map iterates
`subsystems × connectedNodes`, so an undirected edge renders twice as two
overlapping lines.

### 1.4 ResumeModal: pure DOM

Renders résumé text, not a PDF. Five sections: Professional Summary, Technical
Skills, Professional Experience, Selected Development Work, Education.

The a11y implementation is actually decent: `role="dialog"`,
`aria-modal="true"`, `aria-labelledby`, Escape to close, backdrop click to
close, focus restore via a stashed `document.activeElement`, and a focus trap
on the two boundary cases.

**Gaps:** no `inert` on background content, the 20ms `setTimeout` before focusing
is a race, and the trap does not recover focus that escapes mid-list.

### 1.5 ContactSignalNetwork: static SVG, 500x340

4 channels, 2 static "signal packets" that do not travel, a panel that updates
on selection.

**Two defects:** `onMouseEnter` sets state with no `onFocus` equivalent, so
keyboard users get no panel update until they press Enter. And the
`onKeyDown` handler omits the resume branch that `onClick` has, so **the résumé
is unreachable by keyboard from this graph**.

**Third:** `max-h-[340px]` clips rather than scales when the container is
narrower than the viewBox aspect, so the outer channel nodes may crop at 390px
viewport width. Must be checked at 390px.

---

## 2. HOMEPAGE: THE 8 SECTIONS, CONFIRMED

Exactly 8 top-level sections, in order, with verbatim headings.

| # | id | Eyebrow | Heading |
|---|---|---|---|
| 01 | (aria-label only) | `Software Engineering Portfolio · WordPress and PHP` | `Maulik Bhalodiya` (h1) |
| 02 | `what-i-build` | `02 · Core Engineering Capabilities` | `I build systems, not just websites.` |
| 03 | `technical-skills` | `03 · Interactive Technical Ecosystem` | `Verified Technical Stack` |
| 04 | `selected-work` | `04 · Selected Development Work` | `Selected Development Work` |
| 05 | `experience` | `05 · Career Progression and Responsibilities` | `Professional Experience` |
| 06 | `engineering-approach` | `06 · Methodology and System Discipline` | `How I approach engineering` |
| 07 | `about` | `07 · Engineering Profile` | `About Me` |
| 08 | `hiring-cta` | `08 · Employment Opportunities and Technical Evaluation` | `Looking for a WordPress or PHP developer?` |

**Section 04 is internally split** into 04A (independent development, RankKernel
card plus the compact architecture map, on solid `#0A0A0B`) and 04B
(professional work, 5 featured project cards, on `dark-grid`).

**Background rhythm:** dark-grid, solid, dark-grid, solid/dark-grid, solid,
dark-grid, solid, pure black. Section 08 is the only pure `#000000` section.
The rhythm is deliberate and should not be flattened.

### 2.1 Section 08 is the compliance-critical one, and it passes

Heading: `Looking for a WordPress or PHP developer?`

Deck: `I am interested in software engineering roles involving custom WordPress
plugin development, PHP backend architecture, API integrations, payment
workflows and technically challenging systems. Review my resume or connect
directly to discuss how I can contribute to your engineering team.`

CTAs: `View Resume`, `Connect on LinkedIn`, `Email Me`.

**This is seeking employment, not offering freelance services.** It is the
opposite of the banned pattern and it is correctly framed: he wants a job, not
clients. Keep the heading. The `Email Me` CTA must become the contact form.

### 2.2 The rest of the hero copy

Subhead: `WordPress Developer | PHP Engineer | Custom Plugin Developer`
Supporting: `I build custom WordPress solutions, backend systems, plugins and API
integrations for complex technical requirements.`
CTAs: `Explore My Work` (smooth scroll to `#selected-work`), `View Resume`
Bottom strip: `Role: WordPress Developer at Qrolic Technologies`

### 2.3 Two interactive panels in sections 02 and 06

Section 02 has 5 selector buttons driving a spec panel showing Architecture
Focus, Engineering Mandate and Key Technical Mechanisms, plus a link to the
related case study.

Section 06 has 5 stage buttons (Understand, Design, Build, Verify, Improve) with
a deep-dive panel and a "Next Stage" button that cycles.

Both are simple click-to-swap. No animation.

---

## 3. OTHER PAGES

| Route | Sections | Note |
|---|---|---|
| `/about/` | hero + 6 blocks | **Numbering out of order**, 08 renders before 07 |
| `/resume/` | hero + 7 blocks | **Two h1 elements on the page** |
| `/rankkernel/` | hero + 8 blocks | **Numbering out of order**, 09/10 before 08/11 |
| `/contact/` | hero + 1 block | No form exists |
| `/projects/` | hero + filter + 2 sections | See filter defects below |

### 3.1 The résumé page has a structural heading defect

`InnerPageHero` renders an `h1`, then `ResumeDocumentContent` renders **another
h1** inside it. Two `h1` elements on `/resume/`. Real defect, must be fixed.

### 3.2 Section numbering is out of order on two pages

About: the source literally concatenates into one eyebrow
`06 · Independent Development and 08 · Currently Building`, then renders 07
afterwards.

RankKernel: sections 09 and 10 render before 08, and one eyebrow reads
`08 · Open Source Repository and 11 · Contextual Navigation`.

Cosmetic but visible. Fix during the port, do not preserve.

### 3.3 The résumé identity line is inconsistent

Homepage and About use pipes: `WordPress Developer | PHP Engineer | Custom
Plugin Developer`
Résumé page uses: `WordPress Developer | PHP Engineer` and about uses "and".

Normalise to one form. Pipes read as a spec sheet and fit the design.

---

## 4. ACCESSIBILITY: WHAT IS ACTUALLY BROKEN

### 4.1 The serious one: `role="img"` swallows interactive children

Three components put `role="img"` with an `aria-label` on an `<svg>` that
**contains focusable `role="button"` children**. In the accessibility tree,
`role="img"` collapses the subtree to a single leaf node, so the interactive
nodes are very likely unreachable by screen reader.

Affected: TechnicalSkillsEcosystem, RankKernelArchitecture, ContactSignalNetwork.

**Fix:** drop `role="img"` from any SVG that contains interactive children, or
move the interactive layer to sibling HTML. Given three of five components are
static SVG with a parallel HTML control set already present, the cleanest fix is
to make the **HTML controls the accessible interface** and treat the SVG as
`aria-hidden` decoration.

### 4.2 Tablist without tabpanel, three times

Hero, skills, and RankKernel all use `role="tablist"` + `role="tab"` +
`aria-selected` with **no `role="tabpanel"` and no `aria-controls`**. The tabs
have nothing to control. No arrow-key roving tabindex either, so every tab is a
separate tab stop.

The RankKernel status filter is the worst case: it is semantically a **filter**,
not tabs, and should be `role="group"` with toggle buttons carrying
`aria-pressed`.

### 4.3 Focus indicator removed on project card titles

`ProjectCard` applies `focus:outline-none` to the card title link. The global
`:focus-visible` rule is good, but this one element loses it. Must be removed.

### 4.4 Tab-stop explosion

RankKernelArchitecture non-compact mode: 9 node groups + 4 filter tabs + up to 9
connected-node buttons + 9 directory buttons = **25+ tab stops in one component**,
with no arrow-key navigation. Needs roving tabindex or a different control model.

### 4.5 Status conveyed but not announced

RankKernel dims filtered-out nodes with `opacity: 0.25` but leaves them focusable
with unchanged accessible names. A screen reader user gets no signal they are
filtered. Selection changes are not in an `aria-live` region.

### 4.6 What is already good, preserve it

- Global `:focus-visible` 2px solid amber, 3px offset, correctly switching to
  near-black on light surfaces
- Every decorative square and separator has `aria-hidden="true"`
- Homepage sections have `aria-labelledby`; section 01 has an `aria-label`
- Homepage heading order is clean, h1 then seven h2
- Status in RankKernel uses dash patterns plus text, not colour alone
- Modal dialog pattern is correctly implemented
- The canvas has Pause, Resume and Reset controls

### 4.7 Things that disappear or improve in WordPress for free

- **SPA route focus.** No focus is moved to the new h1 on client-side navigation.
  Real page loads fix this entirely.
- **Missing skip link.** `main#main-content` exists, so this is one line.
- **Inconsistent landmarks.** Only homepage sections have accessible names, so
  only some pages expose proper regions.

---

## 5. COMPLIANCE VERDICT

### 5.1 Clean, verified zero

| Check | Result |
|---|---|
| Freelance / hire-me / services offer language | **zero** |
| Em dashes | **zero** |
| En dashes | **zero** |
| Percentages in content | **zero** |
| Star ratings | **zero** |
| Progress bars or skill meters | **zero** |
| Raw project metrics field | **does not exist** |
| Client names | **none, by design** |

The `services` word appears about 10 times in technical prose ("external
services", "backend services"). That is describing work performed, not offering
services. Fine as-is.

### 5.2 Violations to fix

**Raw email, 7 visible occurrences plus 9 mailto links.** Rendered as plaintext
in ContactPage hero metadata and body, ContactSignalNetwork, ResumeModal,
ResumePage (twice). Plus mailto in HomePage (twice), ContactPage (twice),
AboutPage, ResumeModal, ResumePage (twice).

Every one becomes the contact form.

### 5.3 One judgment call

The project titled `Distance Based Dynamic Pricing` has a category of `Backend
Calculation and E Commerce` and an architecture node named `pricing-engine`.

This describes pricing logic the client ran on their own products, not a pricing
service offered to visitors. But **"Dynamic Pricing" as a case study title could
misread as a service** to a skimming recruiter.

It is not on the homepage. It appears only on the archive, its own case study,
and related links. If your rule is strict, rename it. If the rule is about
offering, keep it. **Your call, flagging it because it is the one string in the
whole site that could read commercially.**

### 5.4 The signature separator

`·` (U+00B7) is the universal separator, roughly 100 occurrences, consistently
wrapped in `aria-hidden` spans. Not banned, and it is genuinely part of the
design's voice. Keep it.

---

## 6. EXTERNAL URLS

| URL | Type | Count |
|---|---|---|
| `mailto:maulikbhalodiya9999@gmail.com` | mailto | 9, all to be removed |
| `https://github.com/maulikbhalodiya` | external | 6 |
| `https://www.linkedin.com/in/maulikbhalodiya/` | external | 7 |
| Google Fonts CSS2, 3 families | third-party asset | 1, render-blocking |

Plus 6 plaintext duplicates of the LinkedIn and GitHub handles that are
hardcoded in components rather than read from the data object, which will drift.

**No API endpoints, no analytics, no third-party scripts, no images, no CDN
assets.** The design makes zero network requests at runtime. That is a genuine
performance advantage worth protecting.

Note `rankkernelRepo` points at the GitHub **profile**, not an actual plugin
repository. The "View on GitHub" CTA on the RankKernel page and the archive goes
to a profile. Either create the repo or relabel the CTA.

---

## 7. BUILD SEQUENCE IMPLIED BY THE ANALYSIS

1. Design tokens into `theme.json`, dark palette, two-yellow WCAG fix
2. Templates and parts, with i18n content in patterns
3. Static SVG components as markup, HTML controls as the accessible layer
4. Project content as nested blocks, no CPT
5. Résumé modal, vanilla, with `inert` and `document.fonts.ready`
6. Hero canvas, with IntersectionObserver and visibilitychange pauses
7. **Contact form, greenfield, `core/form`**
8. **Server-generated résumé download, greenfield**
9. Scroll reveals, new work, one mechanism applied consistently
10. Self-hosted subset fonts with metric-matched fallbacks

Items 7, 8 and 9 are new construction, not conversion. They are the schedule
risk.
