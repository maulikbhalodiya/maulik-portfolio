# PHASE 2 · Shell and homepage

**Status: NOT STARTED** · depends on Phase 1

The shell is the frame every page sits in. The homepage is the page a recruiter
judges in ten seconds.

---

## 2.1 Header

Fixed, 64px tall, transparent at rest.

- [ ] Wordmark to `/`, Syne extrabold
- [ ] **Six nav items, no Writing or Blog link.** Home, RankKernel, Projects,
      About, Resume, Contact
- [ ] Active state: amber 2px bottom border, white text
- [ ] Inactive: `#D8D5CA` text, transparent border, hover to `#343434`
- [ ] **Résumé item always amber**, active or not, and it opens the modal
- [ ] Scrolled state past 24px: `#0A0A0B` at 92% with `backdrop-blur-md` and a
      `#262626` border
- [ ] Mobile menu button 44x44, `aria-expanded`, `aria-controls`
- [ ] Escape closes the menu and returns focus to the button
- [ ] Mobile panel ordered list with numeric prefixes
- [ ] **Mail icon included, and this supersedes the earlier "no mail icon
      anywhere" rule.** Every mailto becoming the contact form was the original
      instruction. It was reversed by the owner on 2026-10-02, who was told the
      address had been spam listed once in production and ruled that it ships,
      because the design's Email link is part of the design and replacing it is
      itself a substitution. CI was narrowed rather than removed: `ci.yml`,
      `ai-review.yml`, `ai-audit.yml` and `live-deploy.yml` permit exactly one
      address, `maulikbhalodiya9999@gmail.com`, and reject any other `mailto:` or
      raw address
- [ ] LinkedIn, GitHub and Email, in the design's order. LinkedIn and GitHub are
      `target="_blank" rel="noopener noreferrer"`; the Email anchor carries
      neither, because a `mailto:` opens a local mail client rather than a
      browsing context
- [ ] The Email item is a `wp:html` list item carrying the same
      `wp-social-link wp-social-link site-header__social-link` classes, because
      `core/social-link` keys its icon off Core's fixed service table and would
      render an empty anchor for `service: "mail"`. It therefore inherits the
      identical box and mark as its two siblings
- [ ] Both copies of the row, the bar's and the panel's, come from one shared
      closure in `patterns/header.php`, so they cannot drift
- [ ] Icon buttons 40x40 minimum, which is the touch target floor

The header is `position: fixed`, so the first section on every page needs
`pt-28` to clear it. This is already in the design.

## 2.2 Footer

Pure `#000000`, hidden on print.

- [ ] **5 / 2 / 3 / 2 asymmetric 12-column grid.** Do not balance it to 3/3/3/3.
      The asymmetry is deliberate
- [ ] Columns: identity, Explore, Development, Connect
- [ ] Group headings amber
- [ ] Above the copyright row, a `#262626` rule
- [ ] Copyright year from `current_time()`, not hardcoded
- [ ] Right side mono technical line, per the design
- [ ] **Email link** in the Connect column, carrying the design's own single
      address. This supersedes the earlier "no email link" rule, on the same owner
      decision of 2026-10-02 recorded in 2.1

## 2.3 Homepage, 8 sections

The design has exactly 8 top-level sections in a fixed order. The order and the
background rhythm are both load-bearing.

| # | id | Eyebrow | Heading |
|---|---|---|---|
| 01 | (aria-label) | `Software Engineering Portfolio · WordPress and PHP` | `Maulik Bhalodiya` |
| 02 | `what-i-build` | `02 · Core Engineering Capabilities` | `I build systems, not just websites.` |
| 03 | `technical-skills` | `03 · Interactive Technical Ecosystem` | `Verified Technical Stack` |
| 04 | `selected-work` | `04 · Selected Development Work` | `Selected Development Work` |
| 05 | `experience` | `05 · Career Progression and Responsibilities` | `Professional Experience` |
| 06 | `engineering-approach` | `06 · Methodology and System Discipline` | `How I approach engineering` |
| 07 | `about` | `07 · Engineering Profile` | `About Me` |
| 08 | `hiring-cta` | `08 · Employment Opportunities and Technical Evaluation` | `Looking for a WordPress or PHP developer?` |

- [ ] **Background rhythm preserved:** dark-grid, solid, dark-grid, split, solid,
      dark-grid, solid, pure black. Section 08 is the only `#000000` section
- [ ] Section 04 internally split: RankKernel block on solid, featured project
      cards on dark-grid
- [ ] One `h1`, clean heading order, no skips
- [ ] Every section has an `aria-labelledby` or an `aria-label`
- [ ] The 8x8px amber square opens every eyebrow, `aria-hidden`

### Section 08 is the compliance-critical one, and it passes

Heading: `Looking for a WordPress or PHP developer?`

This is seeking employment. The opposite of the banned pattern, and correctly
framed. **Keep the heading exactly as written.** The only change is the
`Email Me` CTA, which becomes the contact form.

## 2.4 Two interactive panels

**Section 02**, five capability areas, click to inspect. Panel shows
Architecture Focus, Engineering Mandate and Key Technical Mechanisms, plus a
link to the related case study.

**Section 06**, five stages, Understand, Design, Build, Verify, Improve. Deep
dive panel with a Next Stage button that cycles.

- [ ] Both are simple click-to-swap with no animation
- [ ] Keyboard operable, `aria-selected` or `aria-pressed` as appropriate
- [ ] **No `role="tablist"` without `role="tabpanel"`.** That mistake is in the
      prototype three times and must not be carried over

## 2.5 Hero copy, verbatim

- [ ] Subhead: `WordPress Developer | PHP Engineer | Custom Plugin Developer`
- [ ] Supporting: `I build custom WordPress solutions, backend systems, plugins
      and API integrations for complex technical requirements.`
- [ ] CTAs: `Explore My Work` smooth scroll to `#selected-work`, `View Resume`
- [ ] Bottom strip: `Role: WordPress Developer at Qrolic Technologies`
- [ ] **Company name is `Qrolic`.** The resume says "Qrologic" in one place and
      that is wrong

---

## Definition of done

- [ ] Header with scroll state, mobile menu, focus management
- [ ] Footer with the 5/2/3/2 grid
- [ ] All 8 homepage sections in order with the correct background rhythm
- [ ] Section 08 copy unchanged except the email CTA
- [ ] One `h1`, no heading skips
- [ ] Every section labelled for screen readers
- [ ] Zero mailto links, zero raw email
- [ ] Keyboard pass on header and both interactive panels
- [ ] All CI gates green

**Commit:** `feat(home): header, footer, 8-section homepage`
