# PHASE 7 · Accessibility

**Status: NOT STARTED** · depends on all content phases

**Automated tooling catches roughly a third to two-fifths of real issues.** It
cannot verify focus order, focus visibility, reading order, meaningful link text,
or whether the skip link works. The manual pass is not an optional extra.

---

## 7.1 What the design already gets right, and must survive

- [ ] `:focus-visible` 2px solid amber, 3px offset
- [ ] The ring **switches to near-black on light editorial sections.** A genuine
      WCAG requirement, not a style choice
- [ ] `aria-hidden="true"` on every decorative square and every separator
- [ ] Dialog pattern correctly implemented with focus restore
- [ ] RankKernel status by stroke dash pattern **plus** a text label, never
      colour alone
- [ ] Homepage sections carry `aria-labelledby`, section 01 an `aria-label`
- [ ] Homepage heading order clean, `h1` then seven `h2`
- [ ] Canvas has pause, resume and reset with labels
- [ ] Reduced motion handled, including the JS `matchMedia` check that actually
      stops the canvas loop

## 7.2 The eleven defects to fix

### Serious

**1. `role="img"` swallows interactive children.** Three components put
`role="img"` with an `aria-label` on an `<svg>` that contains focusable
`role="button"` children. In the accessibility tree `role="img"` collapses the
subtree to a single leaf, so the interactive nodes are very likely unreachable by
screen reader. Affects the skills ecosystem, the RankKernel architecture, and the
contact signal graph.

Fix: drop `role="img"`, make the HTML controls the accessible layer, treat the
SVG as decorative with `aria-hidden`.

**2. `role="tablist"` with no `role="tabpanel"`, three times.** Hero, skills and
RankKernel all have tabs pointing at nothing, with no `aria-controls` and no
arrow-key roving tabindex, so every tab is a separate tab stop.

Fix: add real panels, or convert to `role="group"` with `aria-pressed` where it
is a filter. The RankKernel status filter is semantically a filter and should not
be tabs at all.

**3. Focus indicator deleted.** `ProjectCard` applies `focus:outline-none` to the
card title link. Remove it.

**4. Tab-stop explosion.** RankKernel non-compact mode: 9 node groups, 4 filter
tabs, up to 9 connected-node buttons, 9 directory buttons. **25 or more tab
stops in one component**, no arrow-key navigation. Needs roving tabindex.

**5. Résumé unreachable by keyboard from the contact graph.** The `onKeyDown`
handler omits the resume branch that `onClick` has.

### Structural

**6.** Contact graph has no `onFocus` handler, so keyboard focus updates nothing
**7.** Two `h1` elements on `/resume/`, because the hero and the document content
each render one
**8.** `h2` to `h4` skip on the RankKernel page, and the component renders an
`h4` with no `h3` above it
**9.** Filtered architecture nodes stay focusable with unchanged accessible names
and no announcement, because dimming is `opacity: 0.25` only
**10.** No skip link, though the `main` target already exists
**11.** Hero canvas has no keyboard equivalent for node selection

## 7.3 Automated gates

- [ ] axe-core against **every** template in `templates/`, not just the homepage.
      That is the whole point of a block theme
- [ ] Tags `wcag2a`, `wcag2aa`, `wcag21a`, `wcag21aa`
- [ ] Zero violations

## 7.4 The manual pass, which no tool replaces

- [ ] **Tab through every page with the mouse unplugged.** Is focus always
      visible? Does it follow visual order? Does it ever get trapped?
- [ ] **Activate the skip link.** Does focus actually move into `<main>`?
- [ ] **Zoom to 400% and to 200%.** Does it reflow? Is there horizontal scroll?
- [ ] **Screen reader pass** on the homepage and one case study
- [ ] **Forced-colors mode.** Is content still visible?
- [ ] Every link has text that makes sense out of context
- [ ] One `h1` per page, no skipped levels, confirmed by reading the DOM
- [ ] Reduced-motion setting honoured by every animated thing

## 7.5 Other review requirements

- [ ] `<html lang>` set
- [ ] No `tabindex` above 0. Ideally none at all
- [ ] `aria-label` present on every icon-only control
- [ ] Form: `<label>` for every input, errors adjacent and programmatically
      associated, **no placeholder as label**
- [ ] Language of page parts marked with `<span lang>` where it differs
- [ ] No "click here", no non-descriptive link text

---

## Definition of done

- [ ] All eleven defects fixed and verified
- [ ] axe-core zero violations on all 9 templates
- [ ] Manual keyboard pass complete on every page
- [ ] 400% zoom reflow confirmed
- [ ] Screen reader pass complete
- [ ] Forced-colors confirmed
- [ ] No focus trap, no invisible focus, no unreachable control
- [ ] All CI gates green

**Commit:** `fix(a11y): eleven accessibility defects plus manual verification`
