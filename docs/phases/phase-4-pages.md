# PHASE 4 · Remaining pages

**Status: NOT STARTED** · depends on Phase 3

Five pages plus the 404. The design's routes map onto WordPress templates almost
one to one.

---

## 4.1 `/projects/` archive

- [ ] `templates/page-projects.html`
- [ ] **Server-side filter links, not client state.** Research is explicit that
      server-side filtering with prefetch hints is strictly better for Core Web
      Vitals than a client-side filter, needs no JavaScript, and works without
      JavaScript
- [ ] `?tech=php` style query parameters, one link per filter
- [ ] `<link rel="prefetch">` for the likely next filters

### The filter has five real defects, all fixed by rebuilding it

The prototype's filter is client-side `useState` with no URL params and no
links. The option list and the data vocabulary are different word sets:

1. `Payment` button matches data tagged `Payments`. Only works via a special case
2. `Integration` matches nothing directly. Special-cased to `Automation` or `API`
3. `Independent` **always returns an empty grid**, so selecting it always shows
   the empty state. The RankKernel card is gated by a separate boolean, so the
   intent works, but the message reads as a bug
4. `Automation` and `Payments` are real tags with no filter button
5. Options use 10 words, data uses 8, and they do not line up

- [ ] Reconcile the vocabulary into one list
- [ ] Empty state copy that does not read as an error
- [ ] `Showing N of 8` count, computed not hardcoded
- [ ] Featured projects first, then the rest

## 4.2 `/about/`

- [ ] `templates/page-about.html`
- [ ] **Verified timeline dates:** PHP Developer Intern Jan to Jun 2025, WordPress
      Developer Jul 2025 to present
- [ ] Rajkot, Gujarat. No phone number on the site
- [ ] B.Tech Computer Engineering, Ganpat University, 2021 to 2025
- [ ] Identity line uses pipes for consistency with the homepage. The résumé
      page currently says "and" in one place, which is drift
- [ ] 9 technical focus areas
- [ ] The 5-stage philosophy block

### Section numbering is broken in the prototype, fix it

- [ ] The prototype concatenates into one eyebrow reading
      `06 · Independent Development and 08 · Currently Building`
- [ ] And renders 07 after 08
- [ ] Renumber sequentially. Do not preserve the error.

## 4.3 `/contact/`

- [ ] `templates/page-contact.html`
- [ ] The signal network graph as nested markup
- [ ] Channel cards: LinkedIn, GitHub, Résumé
- [ ] **No email card, no mailto, no plaintext address.** Seven plaintext
      occurrences and nine mailto links in the prototype, all removed
- [ ] The form itself is Phase 5

## 4.4 `/resume/`

- [ ] `templates/page-resume.html`
- [ ] Five document sections: Professional Summary, Technical Skills,
      Professional Experience, Selected Development Work, Education
- [ ] **One `h1` only.** The prototype has two, because the hero renders one and
      the document content renders another
- [ ] `no-print` on chrome so the print view is the document alone
- [ ] Print styles invert to white background, black text
- [ ] The download button is Phase 5

## 4.5 `/rankkernel/`

- [ ] `templates/page-rankkernel.html`
- [ ] **This ships.** It is the site's own SEO engine, which is the story
- [ ] 9 subsystems with honest status: 9 COMPLETED, 0 RUNNING, 5 PLANNED.
      RUNNING is zero because no module is recorded as mid-build. The five
      PLANNED are deliberate reservations with no implementation
- [ ] The 14-edge dependency graph, which is a real graph and the strongest
      asset on the page
- [ ] Independence notice verbatim: not affiliated with the employer, not client
      work
- [ ] `independentNotice` in the hero metadata panel
- [ ] Fix the numbering error: 09 and 10 render before 08, and one eyebrow reads
      `08 · Open Source Repository and 11 · Contextual Navigation`
- [ ] Replace the hardcoded `All States (9)` with a computed count
- [ ] Deduplicate the overlapping secondary edges, the prototype draws some
      twice

### The theme must not depend on RankKernel in code

- [ ] No `class_exists` checks
- [ ] No conditional rendering
- [ ] If RankKernel were deactivated the site loses SEO features and nothing else
- [ ] The reference is as data only, in this page and one homepage section

## 4.6 `404.html`

- [ ] The design has **no 404 route.** It is an inline fallback, not a page
- [ ] Real template, real URL
- [ ] Eyebrow `404 / ROUTE NOT RESOLVED`, which the design already had and which
      is good
- [ ] Link back to the archive and home

## 4.7 Future blog support, no navigation

- [ ] `templates/home.html` and `templates/single.html` stubbed
- [ ] **No Writing link in nav. No menu item. Not in the footer.**
- [ ] The theme supports posts so the capability exists without advertising it

---

## Definition of done

- [ ] 6 new templates, all with real content
- [ ] Archive filter is server-side links with a reconciled vocabulary
- [ ] Verified dates on About
- [ ] Zero mailto, zero raw email across every page
- [ ] One `h1` per page, no heading skips
- [ ] Section numbering sequential on About and RankKernel
- [ ] Blog templates exist, blog is not in navigation
- [ ] axe-core clean on all 9 templates
- [ ] All CI gates green

**Commit:** `feat(pages): archive, about, contact, resume, rankkernel, 404`
