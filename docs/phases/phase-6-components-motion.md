# PHASE 6 · Components and motion

**Status: NOT STARTED** · depends on Phases 2 to 5

Four of the five interactive components are static SVG or plain DOM, which makes
them markup conversions rather than engineering. Only the hero canvas needs
judgement.

---

## 6.1 Component table

| Component | Technique | Difficulty | Notes |
|---|---|---|---|
| Hero ecosystem | Canvas 2D, hand-rolled 3D math | **real work** | The only one |
| Skills ecosystem | Inline SVG | markup | Coordinates keyed by id |
| RankKernel architecture | Inline SVG | markup | Dedupe edges, computed counts |
| Contact signal | Inline SVG | markup | Keyboard parity, dedupe literals |
| Project card | DOM | markup | Remove `focus:outline-none` |
| Architecture diagram | DOM plus buttons | markup | Key by node id |
| Résumé modal | Vanilla JS | markup | Focus trap plus `inert` |

## 6.2 Hero canvas, the only real engineering

The prototype does 2D canvas with hand-written 3D projection: rotate on Y then X,
perspective divide by `fov / max(distance + z, 120)`. About 300 path operations
per frame across 14 vertices, 24 edges, 2 orbital rings, 8 nodes, 16 mesh edges
and 8 signal packets. Cheap in absolute terms.

### Three real problems in the prototype

1. **The loop never pauses.** No `IntersectionObserver`, no `visibilitychange`.
   It runs at 60fps whenever the hero is in the DOM, even scrolled far out of
   view. Browsers throttle background tabs, but that is browser behaviour, not
   code.
2. **The effect re-runs on every hover change.** The dependency array includes
   the hovered node, so every hover tears down and re-runs the effect.
3. **`getBoundingClientRect()` is called every frame.** A forced layout read per
   frame, plus roughly 30 to 40 short-lived array allocations per frame from
   `.map`, object spread and `.sort`.

- [ ] **Pause off-screen** via `IntersectionObserver`
- [ ] **Pause on hidden tab** via `visibilitychange`
- [ ] No per-frame `getBoundingClientRect`. Cache the rect, read it on resize
- [ ] No per-frame array allocation. Reuse buffers
- [ ] Device pixel ratio capped at 2
- [ ] **`await document.fonts.ready` before the first paint.** The canvas paints
      labels with `ctx.font`, so if the web font has not loaded the labels render
      in system mono and stay wrong until an accidental repaint
- [ ] **`prefers-reduced-motion` checked in JS.** The prototype's global CSS
      `!important` block does **not** stop a canvas rAF loop, so the JS check is
      the only thing that actually works here
- [ ] Pause, Resume and Reset controls kept, with `aria-label`s
- [ ] Node selection reachable by keyboard, not hover only
- [ ] Pause when the canvas is not the active tab

## 6.3 Static SVG components

- [ ] **HTML controls are the accessible layer. The SVG is decorative.**
- [ ] Drop `role="img"` from any SVG containing focusable children, three places
- [ ] **Skills ecosystem: key node coordinates by category id**, not by array
      position. The prototype indexes a positional array, so reordering the
      categories silently breaks the graph
- [ ] **RankKernel: replace the hardcoded `All States (9)`** with a computed
      count
- [ ] **RankKernel: dedupe the overlapping secondary edges.** The prototype
      iterates `subsystems × connectedNodes`, so an undirected edge is drawn
      twice as two overlapping lines
- [ ] **Contact signal: add `onFocus` parity.** `onMouseEnter` sets state with no
      focus equivalent, so keyboard users see nothing happen
- [ ] **Contact signal: fix the keyboard path so Résumé is reachable.** The
      `onKeyDown` handler omits the resume branch that `onClick` has
- [ ] **Contact signal: dedupe the hardcoded LinkedIn and GitHub literals.** They
      are duplicated in the component instead of read from the data object, so
      they will drift
- [ ] **Contact signal: check at 390px.** `max-h-[340px]` clips rather than
      scales, so outer nodes may crop on narrow viewports
- [ ] Unknown node id returns nothing silently. Add a guard or a comment

## 6.4 Project card

- [ ] **Remove `focus:outline-none` from the card title link.** It deletes the
      focus indicator from the one element that most needs it
- [ ] Node labels joined with `/`, technologies with `·`, both `aria-hidden` where
      decorative
- [ ] "Architecture Pipeline" panel showing node count and labels
- [ ] Both variants preserved: cream and dark

## 6.5 Résumé modal

- [ ] `role="dialog"`, `aria-modal="true"`, `aria-labelledby`
- [ ] Focus trap on the two boundary cases, which the prototype does correctly
- [ ] Focus restore via a stashed `document.activeElement`
- [ ] Escape to close, backdrop click to close
- [ ] Body scroll lock
- [ ] **`inert` on background content.** The prototype has no `inert`, so
      `aria-modal` is the only barrier
- [ ] **Replace the 20ms `setTimeout` before focusing.** It is a race and can fail
      on slow paint. Use `requestAnimationFrame`, or focus the dialog container
      with `tabindex="-1"`
- [ ] Trap recovers focus that escapes mid-list
- [ ] **Content is text, not a PDF.** No iframe, no embed

---

## Definition of done

- [ ] Hero canvas pauses off-screen and on hidden tab
- [ ] No per-frame layout read, no per-frame allocation
- [ ] Labels render in IBM Plex Mono, not system mono
- [ ] Reduced motion stops the loop in JS
- [ ] All three SVGs free of `role="img"` over focusable children
- [ ] Coordinates keyed by id everywhere
- [ ] No hardcoded counts, no duplicated literals
- [ ] `focus:outline-none` gone from the project card
- [ ] Modal has `inert`, focus restore, no timing race
- [ ] Contact graph fully keyboard operable
- [ ] All CI gates green

**Commit:** `feat(components): hero canvas, SVG diagrams, resume modal`
