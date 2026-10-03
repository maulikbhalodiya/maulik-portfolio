# Goals, in order

One goal at a time. Each goal has a clear finish line. A goal is only
**COMPLETE** when every acceptance check is **DONE**, verified in a browser.

**Status:** one goal in progress. Later goals are queued, not started.

---

## GOAL 1: Home page complete  ·  `IN PROGRESS`

> Reproduce the approved design's Home page exactly, with working
> interactions, and deliver it for your review before any other page is touched.

### Scope

`/` only. Header, hero, all 8 sections, footer.

### Acceptance criteria

| # | Criterion | Evidence required | Status |
|---|---|---|---|
| 1 | Header active state on every route | click test in browser | DONE |
| 1b | Header transparent until scroll, 64px | measured before and after scroll | DONE |
| 2 | Hero canvas panel inside viewport | measured box, no x-overflow | DONE |
| 2b | Hero icons 16px | measured, was 40px | DONE |
| 3 | **Section 02 capability tabs swap detail panel** | click tab 3 then 5, panel text changes | RUNNING |
| 4 | **Section 03 tabs swap skill panel** | click each tab, card count changes | RUNNING |
| 5 | **Section 03 auto-rotates every 3s** | sampled at 0, 1.5, 3.5, 6.5, 9.5s, advances | RUNNING |
| 6 | **Section 03 hover pauses rotation** | hover panel, sample 4s, not advancing | RUNNING |
| 7 | **Section 06 stage tabs swap panel** | click each stage, panel changes | RUNNING |
| 8 | All surfaces dark, no cream or warm | computed background per section | DONE |
| 9 | Section padding matches design | computed padding per section | DONE |
| 10 | Section count is 8 | counted in DOM | DONE |
| 11 | **Experience card label above title** | measured label top < title top, both cards | QUEUED |
| 12 | **Snapshot cards equal height** | both cards same height, design value | QUEUED |
| 13 | Arrow icons 16px everywhere | measured all `a svg` | DONE |
| 14 | Footer external links serialize correctly | read attributes from live HTML | DONE |
| 15 | Footer matches design | height and link inventory | PART |
| 16 | No horizontal overflow at 1440 | `scrollWidth` equals viewport | DONE |
| 17 | No horizontal overflow at 390 | `scrollWidth` equals viewport | DONE |
| 18 | Tablet 768 reviewed | measured, not yet done | NOT MEASURED |
| 19 | Keyboard works on all three tab groups | arrow, Home, End, Enter, Space | RUNNING |
| 20 | Focus ring visible on all controls | computed `:focus-visible` outline | RUNNING |
| 21 | Reduced motion honoured | emulated, rotation stops, tabs work | RUNNING |
| 22 | No duplicate timers or listeners | instrumented, counted | RUNNING |
| 23 | Zero off-palette colours | computed colour per element | DONE |
| 24 | No em dash, no en dash | grep | DONE |
| 25 | Zero border-radius, zero box-shadow | grep | DONE |
| 26 | Content still in `post_content` | verified, nothing in templates | DONE |

**Finish line:** criteria 3 to 7, 11, 12, 15, 18 to 22 all **DONE**, then you review.

**Not part of this goal:** other pages, contact form, contrast decision,
RankKernel node click.

---

## GOAL 2: About page  ·  `QUEUED`

Reproduce `/about/`. Entry condition: Goal 1 COMPLETE.

| Criterion | Status |
|---|---|
| 5 sections, all dark | DONE |
| Surfaces alternate grid and flat as designed | DONE |
| Inner page padding 96px | DONE |
| Resume actions, design has 4 buttons | PART, we have 2 |
| Hero copy matches design | DONE |

---

## GOAL 3: Contact page  ·  `QUEUED`

Entry: Goal 1 COMPLETE.

| Criterion | Status |
|---|---|
| 2 sections, both dark | DONE |
| Hero height matches | PART, short by 72px |
| **Contact form with intent dropdown** | BLOCKED, needs your decision |
| 3 channel buttons | PART, we have 2 |

---

## GOAL 4: RankKernel page  ·  `QUEUED`

Entry: Goal 1 COMPLETE.

| Criterion | Status |
|---|---|
| 6 sections, all dark | DONE |
| Status filter works, DOM removal not CSS | DONE |
| Keyboard on the filter | DONE |
| **Node click swaps inspector panel** | QUEUED, needs 9 panels of content |
| Closing section height | PART, short |
| Replace `core/html` with a theme block | DEVIATION, needs your decision |

---

## GOAL 5: Projects page polish  ·  `QUEUED`

Entry: Goal 1 COMPLETE. This page is nearly done.

| Criterion | Status |
|---|---|
| 4 sections, all dark | DONE |
| Filter tablist with 10 tabs | DONE |
| Filter narrows the archive | DONE |
| Keyboard on the filter | DONE |
| Section heights | DONE |

---

## GOAL 6: Resume page  ·  `QUEUED`

Entry: Goal 1 COMPLETE. This page is nearly done.

| Criterion | Status |
|---|---|
| 7 sections, all dark | DONE |
| 5 of 7 sections pixel exact | DONE |
| Education section height | DONE, was 9x too tall |
| Print action | PART, design has 3 buttons |
| Hero mailto block | DEVIATION, mailto is forbidden |

---

## GOAL 7: Whole site review  ·  `QUEUED`

Entry: all page goals COMPLETE.

- Every route measured at 1440, 768 and 390
- Every interaction driven with real clicks and keys
- Keyboard pass on every control
- Contrast decision applied
- Every open question closed
- Commit, push, PR opened
- Brutal reviewer pass, then fixes, then re-verify